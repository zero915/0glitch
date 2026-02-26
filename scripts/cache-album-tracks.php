#!/usr/bin/env php
<?php
/**
 * Fetch track names from Spotify for each album in data.php and write cache files.
 * data.php then uses these to show track titles on release cards (from the album’s Spotify link).
 *
 * Requires: SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET (env or .env).
 *
 * Usage: php scripts/cache-album-tracks.php
 */

$root = dirname(__DIR__);
if (is_file($root . '/.env')) {
    foreach (file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\"'");
            if (!array_key_exists($key, $_ENV)) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

$clientId     = getenv('SPOTIFY_CLIENT_ID') ?: ($_ENV['SPOTIFY_CLIENT_ID'] ?? '');
$clientSecret = getenv('SPOTIFY_CLIENT_SECRET') ?: ($_ENV['SPOTIFY_CLIENT_SECRET'] ?? '');

if (!$clientId || !$clientSecret) {
    fwrite(STDERR, "Set SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET (env or .env).\n");
    exit(1);
}

$data   = require $root . '/data.php';
$albums = $data['albums'] ?? [];

$cacheDir = $root . '/cache/album-tracks';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Collect Spotify album IDs from albums
$albumIds = [];
foreach ($albums as $a) {
    $url = $a['links']['spotify'] ?? '';
    if (preg_match('#/album/([a-zA-Z0-9]+)#', $url, $m)) {
        $albumIds[$m[1]] = $a['title'] ?? $m[1];
    }
}

if (empty($albumIds)) {
    fwrite(STDERR, "No albums with Spotify links in data.php.\n");
    exit(0);
}

// Token
$ch = curl_init('https://accounts.spotify.com/api/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
    CURLOPT_HTTPHEADER     => [
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
        'Content-Type: application/x-www-form-urlencoded',
    ],
]);
$tokenResponse = curl_exec($ch);
$tokenHttp     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenHttp !== 200) {
    fwrite(STDERR, "Spotify token error (HTTP $tokenHttp).\n");
    exit(1);
}

$tokenData   = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? null;
if (!$accessToken) {
    fwrite(STDERR, "Spotify token missing access_token.\n");
    exit(1);
}

foreach ($albumIds as $albumId => $albumTitle) {
    $tracks = [];
    $offset = 0;
    $limit  = 50;

    do {
        $url = 'https://api.spotify.com/v1/albums/' . urlencode($albumId) . '/tracks?limit=' . $limit . '&offset=' . $offset;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        ]);
        $body = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http !== 200) {
            fwrite(STDERR, "Album $albumId ($albumTitle): API HTTP $http\n");
            break;
        }

        $data  = json_decode($body, true);
        $items = $data['items'] ?? [];
        foreach ($items as $t) {
            $tracks[] = $t['name'] ?? '';
        }
        $offset += count($items);
        $total = (int) ($data['total'] ?? 0);
    } while ($offset < $total && count($items) === $limit);

    $path = $cacheDir . '/' . $albumId . '.json';
    file_put_contents($path, json_encode($tracks, JSON_UNESCAPED_UNICODE) . "\n");
    echo "Cached " . count($tracks) . " tracks for $albumTitle ($albumId)\n";
}

echo "Done. Track names are read from cache in data.php.\n";
