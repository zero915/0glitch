<?php

// Releases are derived from albums: each album is expanded into track_count entries (image, links, title "Track N", album, year, genres).
// One non-empty link is chosen at random per card when rendering.

// Albums: image, links (spotify, apple, youtube, amazon), title (album name), track_count, year, description, genres
// Track names + per-track links: (1) cache from scripts/cache-album-tracks.php if you have Spotify Premium, OR
// (2) manual 'tracks': array of strings (name only) or array of ['name' => '...', 'spotify' => '...', 'youtube' => '...'] for per-track links.
$albums = [
    [
        'image'        => 'images/halimaw.jpg',
        'links'        => [
            'spotify' => 'https://open.spotify.com/album/0pwVXM5wXNwjH5lbBNhiv3',
            'apple'   => 'https://music.apple.com/us/album/halimaw/1873033857',
            'youtube' => '',
            'amazon'  => 'https://music.amazon.com/albums/B0GK9PZGGT',
        ],
        'title'        => 'Halimaw',
        'track_count'  => 15,
        'year'         => '2026',
        'description'  => 'A modern take on Filipino mythical creatures (monsters) told through music. Some are terrifying, some are romanticized, and some simply live in folklore.',
        'genres'       => ['Pop', 'Bossanova', 'Blues', 'Rock'],
        'tracks'       => [
            ['name' => 'Kalahati',  'spotify' => 'https://open.spotify.com/track/5dKMSXGfV8esruYTqV9kB6', 'youtube' => ''],
            ['name' => 'Usok',      'spotify' => 'https://open.spotify.com/track/6JubvtsyNtUE7NgkabbQ9M', 'youtube' => ''],
            ['name' => 'Tanod',     'spotify' => 'https://open.spotify.com/track/1CrVKl8mjK7r5d3riAIXMy', 'youtube' => ''],
            ['name' => 'Landas',    'spotify' => 'https://open.spotify.com/track/6eh8vQaM0W5umQHjp4UZLv', 'youtube' => ''],
            ['name' => 'Lupa',      'spotify' => 'https://open.spotify.com/track/4DJeFmHs4iL2wV13gxlsoS', 'youtube' => ''],
            ['name' => 'Sumpa',     'spotify' => 'https://open.spotify.com/track/1ymio20n3S6aebVNt4heb6', 'youtube' => ''],
            ['name' => 'Puti',      'spotify' => 'https://open.spotify.com/track/7C5vYnP8IRCvljZHDNcW1S', 'youtube' => ''],
            ['name' => 'Hikbi',     'spotify' => 'https://open.spotify.com/track/3FzU41JoSrHhXx6fVK0hjx', 'youtube' => ''],
            ['name' => 'Bantay',    'spotify' => 'https://open.spotify.com/track/0LInuzUvAR3kBWe9lP0MNn', 'youtube' => ''],
            ['name' => 'Haplos',    'spotify' => 'https://open.spotify.com/track/727yNwaHS7a4J8DbQKwvSz', 'youtube' => ''],
            ['name' => 'Lingon',    'spotify' => 'https://open.spotify.com/track/26mvK2ix2XHPHQh5Dx7DVQ', 'youtube' => ''],
            ['name' => 'Itim',      'spotify' => 'https://open.spotify.com/track/4P74ZcNHdSG907B65k2FFj', 'youtube' => ''],
            ['name' => 'Tunog',     'spotify' => 'https://open.spotify.com/track/3UxGkPLue2SUHoFJx3DFvI', 'youtube' => ''],
            ['name' => 'Liit',      'spotify' => 'https://open.spotify.com/track/6VMRjEF6Kk8LdF94N1hq91', 'youtube' => ''],
            ['name' => 'Ahas',      'spotify' => 'https://open.spotify.com/track/0fN5bHsqyKYZKYs8N92KvC', 'youtube' => ''],
        ],
    ],
    [
        'image'        => 'images/system-upgrade.jpg',
        'links'        => [
            'spotify' => 'https://open.spotify.com/album/3lMnDfREaxXTp72psKWbVS',
            'apple'   => 'https://music.apple.com/us/album/system-upgrade/1859919323',
            'youtube' => 'https://music.youtube.com/playlist?list=OLAK5uy_lWs1DgC2DvpuNABjPqhGnyK8K0y3iRQJM',
            'amazon'  => 'https://music.amazon.com/albums/B0G5YCSNVS',
        ],
        'title'        => 'System Upgrade',
        'track_count'  => 15,
        'year'         => '2025',
        'description'  => 'Full-length upgrade. New sounds, same malfunction.',
        'genres'       => ['Pop', 'Indie', 'Rock', 'Rap'],
        'tracks'       => [
            ['name' => 'System Upgrade', 'spotify' => 'https://open.spotify.com/track/4OmgpP88rPpGNGVKiCR61g', 'youtube' => ''],
            ['name' => 'Ikaw Lang',      'spotify' => 'https://open.spotify.com/track/2HpscrCRIhaQ7hKYn2yDUM', 'youtube' => ''],
            ['name' => 'Fun Run',        'spotify' => 'https://open.spotify.com/track/7A8QY2pTIlNHwQhn0JpEyk', 'youtube' => ''],
            ['name' => 'U + Me',         'spotify' => 'https://open.spotify.com/track/2IGcIbNpaUGi4IjRSHPr7N', 'youtube' => 'https://music.youtube.com/watch?v=fyh4nzAW9-4'],
            ['name' => 'Flex Para Sa’yo','spotify' => 'https://open.spotify.com/track/0PwZzIMmw46S30rLAqj0SH', 'youtube' => 'https://music.youtube.com/watch?v=3UfGHqrdgc0'],
            ['name' => 'Sweet Crazy',    'spotify' => 'https://open.spotify.com/track/6lWy9xOdbIJApLriY9CGJS', 'youtube' => 'https://music.youtube.com/watch?v=FvxK_jo_5IQ'],
            ['name' => 'Lucky Charm',    'spotify' => 'https://open.spotify.com/track/6eb9s5NLc6I9NqkFQ6qbRp', 'youtube' => 'https://music.youtube.com/watch?v=hK0AQD-YseQ'],
            ['name' => 'King Dumbo',     'spotify' => 'https://open.spotify.com/track/4Rq1K26YDOgPAysHi5eW3t', 'youtube' => ''],
            ['name' => 'Silver',         'spotify' => 'https://open.spotify.com/track/7LiuG0R4tlPDMPrhlJ1Aak', 'youtube' => ''],
            ['name' => 'You Don’t Know', 'spotify' => 'https://open.spotify.com/track/0nC2CYz29QcF9htK0Rqnmz', 'youtube' => ''],
            ['name' => 'iMove',          'spotify' => 'https://open.spotify.com/track/4ssMzGURBr7JcVEsFrkHj8', 'youtube' => 'https://music.youtube.com/watch?v=7uIyTu4VF2I'],
            ['name' => 'Golden Fire',    'spotify' => 'https://open.spotify.com/track/10vNExVk9JmCDJrIqYWJiR', 'youtube' => ''],
            ['name' => 'Shadow Play',    'spotify' => 'https://open.spotify.com/track/3rYRGt5yPodYD5fUj3Dcan', 'youtube' => ''],
            ['name' => 'Sinta',          'spotify' => 'https://open.spotify.com/track/7pHcQDiZuBur66S41e2cq2', 'youtube' => ''],
            ['name' => 'Sayaw Sa Hangin','spotify' => 'https://open.spotify.com/track/088yyXF6p9vvXtm7OuV27o', 'youtube' => 'https://music.youtube.com/watch?v=QUBgh4C_eaw'],
        ],
    ],
    [
        'image'        => 'images/merry-christmas-sayo.jpg',
        'links'        => [
            'spotify' => 'https://open.spotify.com/album/3HTKXuQ8iYcrCeER3MwR8n',
            'apple'   => 'https://music.apple.com/us/album/merry-christmas-sayo/1851819376',
            'youtube' => 'https://music.youtube.com/playlist?list=OLAK5uy_mZgFlL7SRaiADMbU5EEWAo7lmsaV0S0Uw',
            'amazon'  => 'https://music.amazon.com/albums/B0FZSJNMLW',
        ],
        'title'        => 'Merry Christmas, Sayo',
        'track_count'  => 9,
        'year'         => '2025',
        'description'  => 'Holiday vibes and winter feels. A short run of seasonal tracks.',
        'genres'       => ['Pop', 'Holiday', 'R&B', 'Rock'],
        'tracks'       => [
            ['name' => 'Astig Na Pasko',              'spotify' => 'https://open.spotify.com/track/737xAq49O9XRtwZt5mSKXc', 'youtube' => ''],
            ['name' => 'Christmas Card',              'spotify' => 'https://open.spotify.com/track/2dO9eLq0FDL2OkbbGBVLKc', 'youtube' => ''],
            ['name' => 'The Gift I Didn’t Know',      'spotify' => 'https://open.spotify.com/track/1eHfDEKOeGPmFprzyAiayb', 'youtube' => ''],
            ['name' => 'We Still Believe',            'spotify' => 'https://open.spotify.com/track/3ZquNTYPdpEOBe0lWDNOUq', 'youtube' => ''],
            ['name' => 'Cookies for Santa',           'spotify' => 'https://open.spotify.com/track/2jc2m1tVoaDNVohM9mmmLP', 'youtube' => ''],
            ['name' => 'Overtime (Sa Christmas Eve)', 'spotify' => 'https://open.spotify.com/track/5T9sOigyBO0bfMrnj4fWPJ', 'youtube' => ''],
            ['name' => 'Me Mom and Santa',            'spotify' => 'https://open.spotify.com/track/31Sn79QThg0KHrklnJnOZT', 'youtube' => ''],
            ['name' => 'Come On In',                  'spotify' => 'https://open.spotify.com/track/36beIT2JVVGqN1XJqAra7G', 'youtube' => ''],
            ['name' => 'Pasko Ng Rock',               'spotify' => 'https://open.spotify.com/track/3jK1DHExF6dAJYWk8nUQaQ', 'youtube' => ''],
        ],
    ],
];

// Load per-track data from cache (name + spotify track URL). Cache: cache/album-tracks/{spotify_album_id}.json
// Format: array of { "name": "...", "spotify": "https://open.spotify.com/track/..." }. Legacy: array of strings = names only.
$cacheDir = __DIR__ . '/cache/album-tracks';
function get_album_tracks_from_cache($cacheDir, $spotifyAlbumUrl) {
    if (!preg_match('#/album/([a-zA-Z0-9]+)#', $spotifyAlbumUrl ?? '', $m)) {
        return null;
    }
    $path = $cacheDir . '/' . $m[1] . '.json';
    if (!is_file($path)) {
        return null;
    }
    $json = @file_get_contents($path);
    if ($json === false) {
        return null;
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
}

// Build releases from albums: one entry per track. Use Spotify cache when it has data; when cache is empty or missing, use manual 'tracks' from data.php.
$releases = [];
foreach ($albums as $a) {
    $year = $a['year'] ?? '2025';
    $albumLinks = [
        'amazon'  => $a['links']['amazon'] ?? '',
        'youtube' => $a['links']['youtube'] ?? '',
        'apple'   => $a['links']['apple'] ?? '',
        'spotify' => $a['links']['spotify'] ?? '',
    ];
    $manualTracks = $a['tracks'] ?? null;
    $cachedTracks = get_album_tracks_from_cache($cacheDir, $a['links']['spotify'] ?? null);
    for ($n = 1; $n <= $a['track_count']; $n++) {
        // Prefer cache when it has a track for this index; otherwise use manual tracks (so data.php is used when Spotify cache is empty)
        $track = null;
        if (!empty($cachedTracks) && isset($cachedTracks[$n - 1])) {
            $track = $cachedTracks[$n - 1];
        } elseif (!empty($manualTracks) && isset($manualTracks[$n - 1])) {
            $track = $manualTracks[$n - 1];
        }
        $trackName = 'Track ' . $n;
        // Start from album links, then override with per-track links so modal uses track URLs
        $releaseLinks = [
            'amazon'  => $a['links']['amazon'] ?? '',
            'youtube' => $a['links']['youtube'] ?? '',
            'apple'   => $a['links']['apple'] ?? '',
            'spotify' => $a['links']['spotify'] ?? '',
        ];
        if (is_array($track)) {
            $trackName = isset($track['name']) && $track['name'] !== '' ? $track['name'] : $trackName;
            if (isset($track['spotify']) && $track['spotify'] !== '') {
                $releaseLinks['spotify'] = $track['spotify'];
            }
            if (isset($track['youtube']) && $track['youtube'] !== '') {
                $releaseLinks['youtube'] = $track['youtube'];
            }
        } elseif (is_string($track) && $track !== '') {
            $trackName = $track;
        }
        $releases[] = [
            'image'  => $a['image'],
            'links'  => $releaseLinks,
            'title'  => $trackName,
            'album'  => $a['title'],
            'year'   => $year,
            'genres' => $a['genres'],
        ];
    }
}

return [
    'releases' => $releases,
    'albums'   => $albums,

    // Platforms data: name, url, icon (lucide), gradient classes, hover border/text
    'platforms' => [
    [
        'name'     => 'Spotify',
        'url'      => 'https://open.spotify.com/artist/6QxFhVRljm0SiM6ubZSYzz',
        'icon'     => 'music',
        'gradient' => 'from-green-400 to-green-600',
        'hover'    => 'green',
    ],
    [
        'name'     => 'Apple Music',
        'url'      => 'https://music.apple.com/us/artist/zero-glitch/1842895473',
        'icon'     => 'apple',
        'gradient' => 'from-red-500 to-pink-600',
        'hover'    => 'red',
    ],
    [
        'name'     => 'YouTube',
        'url'      => 'https://music.youtube.com/channel/UC82iAlbso9-IoCdeBCbSMDQ',
        'icon'     => 'youtube',
        'gradient' => 'from-red-600 to-red-800',
        'hover'    => 'red',
    ],
    [
        'name'     => 'Amazon Music',
        'url'      => 'https://music.amazon.com/artists/B0FT62TRS4/zero-glitch',
        'icon'     => 'shopping-bag',
        'gradient' => 'from-blue-400 to-blue-600',
        'hover'    => 'blue',
    ],
    [
        'name'     => 'iHeart Radio',
        'url'      => 'https://www.iheart.com/artist/zero-glitch-48171167',
        'icon'     => 'heart',
        'gradient' => 'from-red-400 to-red-600',
        'hover'    => 'red',
    ],
    [
        'name'     => 'Tidal',
        'url'      => 'https://tidal.com/artist/67616346',
        'icon'     => 'waves',
        'gradient' => 'from-purple-500 to-indigo-600',
        'hover'    => 'purple',
    ],
    [
        'name'     => 'Pandora',
        'url'      => 'https://www.pandora.com/artist/0-glitch/AR26xxcx9Vb4l4m',
        'icon'     => 'radio',
        'gradient' => 'from-cyan-400 to-blue-500',
        'hover'    => 'cyan',
    ],
    [
        'name'     => 'Joox',
        'url'      => 'https://www.joox.com/artist/1VLESVk10BTN7d1_RLHwQw==',
        'icon'     => 'audio-lines',
        'gradient' => 'from-green-400 to-emerald-600',
        'hover'    => 'green',
    ],
    ],

    // Platform hover styles by key (border + shadow + text color)
    'platformHover' => [
    'green'  => 'hover:border-green-500/50 hover:shadow-[0_0_30px_rgba(29,185,84,0.3)] group-hover:text-green-400',
    'red'    => 'hover:border-red-500/50 hover:shadow-[0_0_30px_rgba(252,60,68,0.3)] group-hover:text-red-400',
    'blue'   => 'hover:border-blue-500/50 hover:shadow-[0_0_30px_rgba(0,150,255,0.3)] group-hover:text-blue-400',
    'purple' => 'hover:border-purple-500/50 hover:shadow-[0_0_30px_rgba(147,51,234,0.3)] group-hover:text-purple-400',
    'cyan'   => 'hover:border-cyan-500/50 hover:shadow-[0_0_30px_rgba(6,182,212,0.3)] group-hover:text-cyan-400',
    ],

    // Subscribe form → Google Forms (0glitch subscribe)
    // Get email_entry_id: In the form editor click ⋮ (More) → "Get pre-filled link" → type any email in the Email field → "Get link". The URL will contain entry.XXXXXXXXX=... — use that number here.
    'google_form' => [
        'url'             => 'https://docs.google.com/forms/d/e/1FAIpQLSfnFDMQeqtx8UApHZUHTrhTFddbQyjFoyDSWxQkbh6VG420tA/formResponse',
        'email_entry_id'  => '', // e.g. 1234567890 — get from "Get pre-filled link" in the form editor
    ],
];
