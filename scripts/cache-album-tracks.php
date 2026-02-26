#!/usr/bin/env php
<?php
/**
 * Fetch track names + per-track Spotify URLs from Spotify for each album and write cache files.
 * data.php uses cache when present; otherwise use manual 'tracks' in each album in data.php.
 *
 * Requires: SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET (env or .env), and a Spotify Premium
 * subscription (Spotify blocks Web API access without Premium). If you don't have Premium,
 * add track names (and optional per-track 'spotify' URLs) in the 'tracks' array in data.php instead.
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
    $envPath = $root . '/.env';
    fwrite(STDERR, "Spotify credentials missing.\n\n");
    fwrite(STDERR, "1. Go to https://developer.spotify.com/dashboard and create an app (or use an existing one).\n");
    fwrite(STDERR, "2. Copy .env.example to .env in the project root:\n");
    fwrite(STDERR, "   cp .env.example .env\n");
    fwrite(STDERR, "3. Edit .env and set your Client ID and Client Secret:\n");
    fwrite(STDERR, "   SPOTIFY_CLIENT_ID=your_client_id_here\n");
    fwrite(STDERR, "   SPOTIFY_CLIENT_SECRET=your_client_secret_here\n\n");
    fwrite(STDERR, "   (Find them in your app's Settings in the Spotify dashboard.)\n");
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

// Optional: market (e.g. US, PH). Empty = don't send market (can avoid 403 for some albums/regions).
$market = getenv('SPOTIFY_MARKET') ?: ($_ENV['SPOTIFY_MARKET'] ?? '');

// Token (using file_get_contents so cURL is not required)
$tokenOpts = [
    'http' => [
        'method'  => 'POST',
        'header'   => "Authorization: Basic " . base64_encode($clientId . ':' . $clientSecret) . "\r\nContent-Type: application/x-www-form-urlencoded\r\n",
        'content'  => 'grant_type=client_credentials',
        'ignore_errors' => true,
    ],
];
$tokenResponse = @file_get_contents('https://accounts.spotify.com/api/token', false, stream_context_create($tokenOpts));
$tokenHttp = 0;
if (isset($http_response_header) && preg_match('/^HTTP\/\S+\s+(\d+)/', $http_response_header[0], $m)) {
    $tokenHttp = (int) $m[1];
}

if ($tokenHttp !== 200 || $tokenResponse === false) {
    fwrite(STDERR, "Spotify token error (HTTP $tokenHttp). " . ($tokenResponse ?: "No response.") . "\n");
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
        if ($market !== '') {
            $url .= '&market=' . urlencode($market);
        }
        $opts = [
            'http' => [
                'method'  => 'GET',
                'header'   => "Authorization: Bearer " . $accessToken . "\r\n",
                'ignore_errors' => true,
            ],
        ];
        $body = @file_get_contents($url, false, stream_context_create($opts));
        $http = 0;
        if (isset($http_response_header) && preg_match('/^HTTP\/\S+\s+(\d+)/', $http_response_header[0], $hm)) {
            $http = (int) $hm[1];
        }

        if ($http !== 200 || $body === false) {
            fwrite(STDERR, "Album $albumId ($albumTitle): API HTTP $http\n");
            if ($body !== false && $body !== '') {
                $err = json_decode($body, true);
                $msg = isset($err['error']['message']) ? $err['error']['message'] : trim(substr($body, 0, 200));
                fwrite(STDERR, "  Response: $msg\n");
            }
            break;
        }

        $data  = json_decode($body, true);
        $items = $data['items'] ?? [];
        foreach ($items as $t) {
            $tracks[] = [
                'name'    => $t['name'] ?? '',
                'spotify' => $t['external_urls']['spotify'] ?? '',
            ];
        }
        $offset += count($items);
        $total = (int) ($data['total'] ?? 0);
    } while ($offset < $total && count($items) === $limit);

    $path = $cacheDir . '/' . $albumId . '.json';
    file_put_contents($path, json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n");
    echo "Cached " . count($tracks) . " tracks for $albumTitle ($albumId)\n";
}

echo "Done. Track names are read from cache in data.php.\n";
