#!/usr/bin/env php
<?php
/**
 * Fetch album track list from Spotify Web API (track names + Spotify track URLs).
 *
 * Use this to fill in missing songs in data.php. Requires Spotify app credentials.
 *
 * Setup:
 * 1. Create an app at https://developer.spotify.com/dashboard
 * 2. Set Client ID and Client Secret in environment or .env:
 *    SPOTIFY_CLIENT_ID=your_client_id
 *    SPOTIFY_CLIENT_SECRET=your_client_secret
 *
 * Usage:
 *   php scripts/fetch-album-tracks.php [album_id]
 *
 * Examples:
 *   php scripts/fetch-album-tracks.php
 *   php scripts/fetch-album-tracks.php 3lMnDfREaxXTp72psKWbVS
 *   php scripts/fetch-album-tracks.php "https://open.spotify.com/album/3lMnDfREaxXTp72psKWbVS"
 *
 * Output: track number, title, Spotify track URL. Use --php to print data.php-style array entries.
 */

$albumId = $argv[1] ?? '3lMnDfREaxXTp72psKWbVS';
if (preg_match('#/album/([a-zA-Z0-9]+)#', $albumId, $m)) {
    $albumId = $m[1];
}

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
    fwrite(STDERR, "Error: Set SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET (env or .env in project root).\n");
    fwrite(STDERR, "Get them from https://developer.spotify.com/dashboard\n");
    exit(1);
}

// Client Credentials token
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
$tokenHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenHttp !== 200) {
    fwrite(STDERR, "Spotify token error (HTTP $tokenHttp): $tokenResponse\n");
    exit(1);
}

$tokenData = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? null;
if (!$accessToken) {
    fwrite(STDERR, "Spotify token response missing access_token.\n");
    exit(1);
}

// Fetch album tracks (paginate if needed)
$tracks = [];
$offset = 0;
$limit = 50;

do {
    $url = 'https://api.spotify.com/v1/albums/' . urlencode($albumId) . '/tracks?limit=' . $limit . '&offset=' . $offset;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
    ]);
    $body = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http !== 200) {
        fwrite(STDERR, "Spotify API error (HTTP $http): $body\n");
        exit(1);
    }

    $data = json_decode($body, true);
    $items = $data['items'] ?? [];
    foreach ($items as $t) {
        $tracks[] = [
            'track_number' => $t['track_number'] ?? count($tracks) + 1,
            'name'         => $t['name'] ?? '',
            'spotify_url'  => $t['external_urls']['spotify'] ?? ('https://open.spotify.com/track/' . ($t['id'] ?? '')),
            'id'           => $t['id'] ?? '',
        ];
    }
    $offset += count($items);
    $total = (int) ($data['total'] ?? 0);
} while ($offset < $total && count($items) === $limit);

$outputPhp = in_array('--php', array_slice($argv, 1), true);

if ($outputPhp) {
    echo "// Paste these into data.php 'releases' (replace Track 7..15 placeholders).\n";
    foreach ($tracks as $t) {
        $titleEsc = addcslashes($t['name'], "'\\");
        $imgSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($t['name'])));
        $imgSlug = trim($imgSlug, '-') ?: 'system-upgrade';
        echo "[\n";
        echo "    'image'   => 'images/{$imgSlug}.jpg',\n";
        echo "    'links'   => [\n";
        echo "        'amazon'  => 'https://music.amazon.com/albums/B0G5YCSNVS',\n";
        echo "        'youtube' => 'https://music.youtube.com/playlist?list=OLAK5uy_lWs1DgC2DvpuNABjPqhGnyK8K0y3iRQJM',\n";
        echo "        'apple'   => 'https://music.apple.com/us/album/system-upgrade/1859919323',\n";
        echo "        'spotify' => '" . addcslashes($t['spotify_url'], "'\\") . "',\n";
        echo "    ],\n";
        echo "    'title'   => '{$titleEsc}',\n";
        echo "    'album'   => 'System Upgrade',\n";
        echo "    'year'    => '2025',\n";
        echo "    'genres'  => ['Pop', 'Indie', 'Rock', 'Rap'],\n";
        echo "],\n";
    }
} else {
    echo "Album ID: $albumId\n";
    echo str_repeat('-', 80) . "\n";
    printf("%2s  %-40s  %s\n", '#', 'Title', 'Spotify track URL');
    echo str_repeat('-', 80) . "\n";
    foreach ($tracks as $t) {
        printf("%2d  %-40s  %s\n", $t['track_number'], $t['name'], $t['spotify_url']);
    }
    echo "\nRun with --php to output data.php-style array entries.\n";
}
