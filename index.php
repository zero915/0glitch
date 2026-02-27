<?php 

//header('Location: https://music.youtube.com/channel/UC82iAlbso9-IoCdeBCbSMDQ');
//header('Location: https://open.spotify.com/artist/6QxFhVRljm0SiM6ubZSYzz');
//header('Location: https://music.apple.com/us/artist/zero-glitch/1842895473');

$root = __DIR__;
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

$data = require __DIR__ . '/data.php';
$releases = $data['releases'];
$albums = $data['albums'];
$platformHover = $data['platformHover'];

// Platforms: show 8 total — always Spotify, Amazon, YouTube Music, Apple Music; then 4 random from the rest
$allPlatforms = $data['platforms'];
$alwaysShowNames = ['Spotify', 'YouTube Music', 'Apple Music', 'Amazon Music'];
$alwaysPlatforms = [];
foreach ($alwaysShowNames as $name) {
    foreach ($allPlatforms as $p) {
        if (($p['name'] ?? '') === $name) {
            $alwaysPlatforms[] = $p;
            break;
        }
    }
}
$otherPlatforms = array_values(array_filter($allPlatforms, function ($p) use ($alwaysShowNames) {
    return !in_array($p['name'] ?? '', $alwaysShowNames);
}));
shuffle($otherPlatforms);
$randomFour = array_slice($otherPlatforms, 0, 4);
$platforms = array_merge($alwaysPlatforms, $randomFour);

// Build YouTube and Spotify embed URLs for a release (for in-site player). Prefer YouTube when available.
function release_embed_urls(array $links) {
    $out = ['youtube' => '', 'spotify' => ''];
    if (!empty($links['youtube'])) {
        $url = $links['youtube'];
        if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $m) || preg_match('#youtu\.be/([a-zA-Z0-9_-]+)#', $url, $m)) {
            $out['youtube'] = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1';
        } elseif (preg_match('/[?&]list=([a-zA-Z0-9_-]+)/', $url, $m)) {
            $out['youtube'] = 'https://www.youtube.com/embed/videoseries?list=' . $m[1] . '&autoplay=1';
        }
    }
    if (!empty($links['spotify']) && preg_match('#/(track|album)/([a-zA-Z0-9]+)#', $links['spotify'], $m)) {
        $out['spotify'] = 'https://open.spotify.com/embed/' . $m[1] . '/' . $m[2];
    }
    return $out;
}

?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-N5KQJ3B26F"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-N5KQJ3B26F');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero Glitch | Musician + Song Writer</title>
    <meta name="description" content="Official website of Zero Glitch - Musician and Song Writer. Stream on Spotify, Apple Music, YouTube Music, and more.">
    
    <!-- Favicon (0G logo, white on transparent) -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&family=Space+Grotesk:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        glitch: {
                            cyan: '#00f3ff',
                            magenta: '#ff00ff',
                            dark: '#0a0a0a',
                            surface: '#141414',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-glitch-dark text-white font-sans overflow-x-hidden">
    
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-glitch-dark/80 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="font-display font-bold text-2xl tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-glitch-cyan to-glitch-magenta hover:opacity-90 transition-opacity">
                        ZERO GLITCH
                    </a>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-gray-300 hover:text-glitch-cyan transition-colors duration-300">Home</a>
                    <a href="#about" class="text-gray-300 hover:text-glitch-cyan transition-colors duration-300">About</a>
                    <a href="#music" class="text-gray-300 hover:text-glitch-cyan transition-colors duration-300">Music</a>
                    <a href="#platforms" class="text-gray-300 hover:text-glitch-cyan transition-colors duration-300">Platforms</a>
                    <a href="#contact" class="text-gray-300 hover:text-glitch-cyan transition-colors duration-300">Contact</a>
                </div>
                
                <button class="md:hidden text-white" id="mobile-menu-btn">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-glitch-surface border-b border-white/10" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#home" class="block px-3 py-2 text-base font-medium text-gray-300 hover:text-glitch-cyan hover:bg-white/5 rounded-md">Home</a>
                <a href="#about" class="block px-3 py-2 text-base font-medium text-gray-300 hover:text-glitch-cyan hover:bg-white/5 rounded-md">About</a>
                <a href="#music" class="block px-3 py-2 text-base font-medium text-gray-300 hover:text-glitch-cyan hover:bg-white/5 rounded-md">Music</a>
                <a href="#platforms" class="block px-3 py-2 text-base font-medium text-gray-300 hover:text-glitch-cyan hover:bg-white/5 rounded-md">Platforms</a>
                <a href="#contact" class="block px-3 py-2 text-base font-medium text-gray-300 hover:text-glitch-cyan hover:bg-white/5 rounded-md">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16">
        <!-- Background Effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-glitch-dark via-glitch-surface to-glitch-dark"></div>
        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%2300f3ff\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="space-y-8">
                <div class="inline-block">
                    <span class="px-4 py-2 rounded-full border border-glitch-cyan/30 bg-glitch-cyan/10 text-glitch-cyan text-sm font-medium tracking-wider uppercase animate-pulse">
                        New Single Out Now
                    </span>
                </div>
                
                <h1 class="font-display font-black text-6xl md:text-8xl lg:text-9xl tracking-tighter glitch-text" data-text="ZERO GLITCH">
                    ZERO GLITCH
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-400 max-w-2xl mx-auto font-light">
                    Musician • Artist • Nerd
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
                    <a href="#platforms" class="group relative px-8 py-4 bg-glitch-cyan text-glitch-dark font-bold rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-[0_0_30px_rgba(0,243,255,0.5)]">
                        <span class="relative z-10 flex items-center gap-2">
                            <i data-lucide="play" class="w-5 h-5"></i>
                            Listen Now
                        </span>
                    </a>
                    <a href="#about" class="px-8 py-4 border border-white/20 rounded-full font-medium hover:bg-white/5 transition-all duration-300 hover:border-glitch-magenta hover:text-glitch-magenta">
                        Learn More
                    </a>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <i data-lucide="chevron-down" class="w-8 h-8 text-gray-500"></i>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-glitch-cyan to-glitch-magenta rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative rounded-2xl overflow-hidden bg-glitch-surface aspect-square about-hero-media">
                        <img src="images/zero-glitch.jpg" alt="Zero Glitch Artist Portrait" class="about-hero-poster w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-500">
                        <video class="about-hero-video absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300" muted loop playsinline preload="metadata" aria-hidden="true">
                            <source src="videos/zero-glitch.mp4" type="video/mp4">
                        </video>
                        <div class="absolute inset-0 bg-gradient-to-t from-glitch-dark via-transparent to-transparent pointer-events-none"></div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <h2 class="font-display text-4xl md:text-5xl font-bold text-white">
                        Breaking the <span class="text-glitch-cyan">Silence</span>
                    </h2>
                    <div class="space-y-4 text-gray-300 text-lg leading-relaxed">
                        <p>
                            Frustrated artist disguised as an electronics engineer. I make websites to pay the bills, doodle to stay sane, and give cringy, nerdy jokes no one asked for. Half logic, half chaos -- 100% me.
                        </p>
                        <p>
                            My brain is always in conflict. Part nerd, part Matrix fanatic, part hacker wannabe, part oddball. I usually like what others don't. I don't just make music -- it's the sound of a beautiful malfunction.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 pt-6">
                        <div class="text-center p-4 bg-glitch-surface rounded-lg border border-white/5 hover:border-glitch-cyan/50 transition-colors">
                            <div class="text-3xl font-bold text-glitch-magenta mb-1">10</div>
                            <div class="text-sm text-gray-400">Singles</div>
                        </div>
                        <div class="text-center p-4 bg-glitch-surface rounded-lg border border-white/5 hover:border-glitch-cyan/50 transition-colors">
                            <div class="text-3xl font-bold text-glitch-cyan mb-1">3</div>
                            <div class="text-sm text-gray-400">Albums Released</div>
                        </div>
                        <div class="text-center p-4 bg-glitch-surface rounded-lg border border-white/5 hover:border-glitch-cyan/50 transition-colors">
                            <div class="text-3xl font-bold text-glitch-magenta mb-1">50+</div>
                            <div class="text-sm text-gray-400">Countries</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Releases -->
    <section id="music" class="py-24 bg-glitch-surface/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-display text-4xl md:text-5xl font-bold mb-4">Featured <span class="text-glitch-magenta">Tracks</span></h2>
                <p class="text-gray-400">
                    It sounds intentional, not random.
                    <!-- Broadcasting from the lab -->
                    <!-- Handpicked transmissions -->
                    <!-- Curated from the Zero Glitch vault -->
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="featured-tracks">
                <?php
                // Sept 9 - Jan 18: always show Merry Christmas Sa'yo tracks (HIDE_MERRY_CHRISTMAS_SAYO ignored). Outside that window, respect the env var.
                $month = (int) date('n');
                $day = (int) date('j');
                $inChristmasWindow = ($month === 9 && $day >= 9) || in_array($month, [10, 11, 12], true) || ($month === 1 && $day <= 18);
                $releasesShown = $releases;
                if (!$inChristmasWindow) {
                    $hideChristmasEnv = getenv('HIDE_MERRY_CHRISTMAS_SAYO');
                    $hideMerryChristmas = ($hideChristmasEnv === false || $hideChristmasEnv === '') ? true : filter_var($hideChristmasEnv, FILTER_VALIDATE_BOOLEAN);
                    if ($hideMerryChristmas) {
                        $releasesShown = array_values(array_filter($releasesShown, function ($r) {
                            return ($r['album'] ?? '') !== "Merry Christmas Sa'yo";
                        }));
                    }
                }
                shuffle($releasesShown);
                $releasesShown = array_slice($releasesShown, 0, 4);
                foreach ($releasesShown as $r):
                    $links = $r['links'] ?? [];
                    $releaseLinks = array_filter($links);
                    $embeds = release_embed_urls($links);
                    $hasEmbed = $embeds['youtube'] !== '' || $embeds['spotify'] !== '';
                    $externalLink = !empty($releaseLinks) ? $releaseLinks[array_rand($releaseLinks)] : '';
                    // Use this release's track links for modal (not album); embeds are built from same $links
                ?>
                <?php $trackVideo = $r['video'] ?? ''; $trackImage = $r['image'] ?? ''; ?>
                <div class="group relative bg-glitch-dark rounded-xl overflow-hidden border border-white/5 hover:border-glitch-cyan/50 transition-all duration-300 hover:transform hover:scale-105 block cursor-pointer"
                     role="button" tabindex="0" data-track-card
                     data-title="<?php echo htmlspecialchars($r['title']); ?>"
                     data-album="<?php echo htmlspecialchars($r['album']); ?>"
                     data-video="<?php echo htmlspecialchars($trackVideo); ?>"
                     data-youtube-embed="<?php echo htmlspecialchars($embeds['youtube']); ?>"
                     data-spotify-embed="<?php echo htmlspecialchars($embeds['spotify']); ?>"
                     data-spotify-link="<?php echo htmlspecialchars($links['spotify'] ?? ''); ?>"
                     data-youtube-link="<?php echo htmlspecialchars($links['youtube'] ?? ''); ?>"
                     data-amazon-link="<?php echo htmlspecialchars($links['amazon'] ?? ''); ?>"
                     data-apple-link="<?php echo htmlspecialchars($links['apple'] ?? ''); ?>"
                     data-external-link="<?php echo htmlspecialchars($externalLink); ?>">
                    <div class="aspect-square overflow-hidden relative">
                        <img src="<?php echo htmlspecialchars($trackImage); ?>" alt="<?php echo htmlspecialchars($r['title']); ?> - Cover" class="track-card-poster w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php if ($trackVideo !== ''): ?>
                        <video class="track-card-video absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300" muted loop playsinline autoplay preload="metadata" aria-hidden="true">
                            <source src="<?php echo htmlspecialchars($trackVideo); ?>" type="video/mp4">
                        </video>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <span class="w-16 h-16 bg-glitch-cyan rounded-full flex items-center justify-center text-glitch-dark group-hover:scale-110 transition-transform">
                                <i data-lucide="play" class="w-8 h-8 fill-current"></i>
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-bold text-white mb-1"><?php echo htmlspecialchars($r['title']); ?></h3>
                        <p class="text-gray-400 text-xs mb-2"><?php echo htmlspecialchars($r['album']); ?> • <?php echo htmlspecialchars($r['year']); ?></p>
                        <div class="flex gap-1 flex-wrap">
                            <?php foreach ($r['genres'] as $i => $genre): ?>
                            <span class="text-xs px-2 py-0.5 rounded <?php echo $i % 2 === 0 ? 'bg-glitch-cyan/10 text-glitch-cyan border border-glitch-cyan/20' : 'bg-glitch-magenta/10 text-glitch-magenta border border-glitch-magenta/20'; ?>"><?php echo htmlspecialchars($genre); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Latest Albums -->
    <section id="albums" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="font-display text-4xl md:text-5xl font-bold mb-4">Latest <span class="text-glitch-cyan">Albums</span></h2>
                <p class="text-gray-400">New sounds, freshly released.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($albums as $a):
                    $albumLinks = $a['links'] ?? [];
                    $hasSpotify = !empty($albumLinks['spotify']);
                    $hasApple = !empty($albumLinks['apple']);
                    $hasYoutube = !empty($albumLinks['youtube']);
                    $hasAmazon = !empty($albumLinks['amazon']);
                    $hasIheart = !empty($albumLinks['iheart']);
                    $hasTidal = !empty($albumLinks['tidal']);
                    $albumEmbeds = release_embed_urls($albumLinks);
                    $canPlayAlbum = $albumEmbeds['youtube'] !== '' || $albumEmbeds['spotify'] !== '';
                    $albumTrackNames = array_map(function ($t) {
                        return is_array($t) ? (isset($t['name']) ? $t['name'] : '') : (string) $t;
                    }, $a['tracks'] ?? []);
                    $albumTrackSpotifyEmbeds = [];
                    foreach ($a['tracks'] ?? [] as $t) {
                        $url = is_array($t) ? ($t['spotify'] ?? '') : '';
                        if ($url !== '' && preg_match('#/(track|album)/([a-zA-Z0-9]+)#', $url, $m)) {
                            $albumTrackSpotifyEmbeds[] = 'https://open.spotify.com/embed/' . $m[1] . '/' . $m[2];
                        } else {
                            $albumTrackSpotifyEmbeds[] = '';
                        }
                    }
                    $ytListId = '';
                    if (!empty($albumEmbeds['youtube']) && preg_match('/[?&]list=([a-zA-Z0-9_-]+)/', $albumEmbeds['youtube'], $m)) {
                        $ytListId = $m[1];
                    }
                ?>
                <div class="bg-glitch-surface rounded-xl overflow-hidden border border-white/5 hover:border-glitch-cyan/30 transition-all duration-300 flex flex-col">
                    <div class="aspect-square overflow-hidden relative flex-shrink-0 group/cover">
                        <?php if ($canPlayAlbum): ?>
                        <button type="button" class="absolute inset-0 w-full h-full flex items-center justify-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-glitch-cyan focus:ring-inset z-0" aria-label="Play album: <?php echo htmlspecialchars($a['title']); ?>"
                            data-album-play-card
                            data-title="<?php echo htmlspecialchars($a['title']); ?>"
                            data-album="<?php echo htmlspecialchars($a['title']); ?> • <?php echo (int) ($a['track_count'] ?? 0); ?> tracks"
                            data-tracks="<?php echo htmlspecialchars(json_encode($albumTrackNames), ENT_QUOTES, 'UTF-8'); ?>"
                            data-track-spotify-embeds="<?php echo htmlspecialchars(json_encode($albumTrackSpotifyEmbeds), ENT_QUOTES, 'UTF-8'); ?>"
                            data-youtube-list-id="<?php echo htmlspecialchars($ytListId); ?>"
                            data-youtube-embed="<?php echo htmlspecialchars($albumEmbeds['youtube']); ?>"
                            data-spotify-embed="<?php echo htmlspecialchars($albumEmbeds['spotify']); ?>"
                            data-youtube-link="<?php echo htmlspecialchars($albumLinks['youtube'] ?? ''); ?>"
                            data-spotify-link="<?php echo htmlspecialchars($albumLinks['spotify'] ?? ''); ?>"
                            data-amazon-link="<?php echo htmlspecialchars($albumLinks['amazon'] ?? ''); ?>"
                            data-apple-link="<?php echo htmlspecialchars($albumLinks['apple'] ?? ''); ?>">
                            <img src="<?php echo htmlspecialchars($a['image']); ?>" alt="<?php echo htmlspecialchars($a['title']); ?> - Album Cover" class="w-full h-full object-cover group-hover/cover:scale-105 transition-transform duration-300 pointer-events-none">
                            <span class="absolute inset-0 bg-black/40 opacity-0 group-hover/cover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                <span class="w-16 h-16 bg-glitch-cyan rounded-full flex items-center justify-center text-glitch-dark scale-90 group-hover/cover:scale-110 transition-transform">
                                    <i data-lucide="play" class="w-8 h-8 fill-current ml-0.5"></i>
                                </span>
                            </span>
                        </button>
                        <?php else: ?>
                        <img src="<?php echo htmlspecialchars($a['image']); ?>" alt="<?php echo htmlspecialchars($a['title']); ?> - Album Cover" class="w-full h-full object-cover">
                        <?php endif; ?>
                        <div class="absolute bottom-0 left-0 right-0 flex justify-center gap-2 p-3 bg-gradient-to-t from-black/80 via-black/50 to-transparent z-10">
                            <?php if ($hasSpotify): ?>
                            <a href="<?php echo htmlspecialchars($albumLinks['spotify']); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border-2 border-white/50 bg-white/5 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 hover:border-white/80 hover:scale-110 transition-all duration-200" title="Listen on Spotify"><img src="icons/spotify.svg" alt="Spotify" class="w-4 h-4" width="16" height="16"></a>
                            <?php endif; ?>
                            <?php if ($hasApple): ?>
                            <a href="<?php echo htmlspecialchars($albumLinks['apple']); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border-2 border-white/50 bg-white/5 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 hover:border-white/80 hover:scale-110 transition-all duration-200" title="Listen on Apple Music"><img src="icons/applemusic.svg" alt="Apple Music" class="w-4 h-4" width="16" height="16"></a>
                            <?php endif; ?>
                            <?php if ($hasYoutube): ?>
                            <a href="<?php echo htmlspecialchars($albumLinks['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border-2 border-white/50 bg-white/5 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 hover:border-white/80 hover:scale-110 transition-all duration-200" title="Listen on YouTube Music"><img src="icons/youtube.svg" alt="YouTube Music" class="w-4 h-4" width="16" height="16"></a>
                            <?php endif; ?>
                            <?php if ($hasAmazon): ?>
                            <a href="<?php echo htmlspecialchars($albumLinks['amazon']); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border-2 border-white/50 bg-white/5 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 hover:border-white/80 hover:scale-110 transition-all duration-200" title="Listen on Amazon Music"><img src="icons/amazon.svg" alt="Amazon Music" class="w-4 h-4" width="16" height="16"></a>
                            <?php endif; ?>
                            <?php if ($hasIheart): ?>
                            <a href="<?php echo htmlspecialchars($albumLinks['iheart']); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border-2 border-white/50 bg-white/5 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 hover:border-white/80 hover:scale-110 transition-all duration-200" title="Listen on iHeart Radio"><img src="icons/iheartradio.svg" alt="iHeart Radio" class="w-4 h-4" width="16" height="16"></a>
                            <?php endif; ?>
                            <?php if ($hasTidal): ?>
                            <a href="<?php echo htmlspecialchars($albumLinks['tidal']); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border-2 border-white/50 bg-white/5 backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 hover:border-white/80 hover:scale-110 transition-all duration-200" title="Listen on Tidal"><img src="icons/tidal.svg" alt="Tidal" class="w-4 h-4" width="16" height="16"></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="text-xl font-bold text-white mb-1"><?php echo htmlspecialchars($a['title']); ?></h3>
                        <p class="text-gray-500 text-sm mb-2"><?php echo (int) ($a['track_count'] ?? 0); ?> tracks</p>
                        <p class="text-gray-400 text-sm mb-4 flex-1"><?php echo htmlspecialchars($a['description'] ?? ''); ?></p>
                        <div class="flex gap-2 flex-wrap">
                            <?php foreach (($a['genres'] ?? []) as $i => $genre): ?>
                            <span class="text-xs px-2 py-1 rounded <?php echo $i % 2 === 0 ? 'bg-glitch-cyan/10 text-glitch-cyan border border-glitch-cyan/20' : 'bg-glitch-magenta/10 text-glitch-magenta border border-glitch-magenta/20'; ?>"><?php echo htmlspecialchars($genre); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Platforms Section -->
    <section id="platforms" class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-glitch-magenta/5 to-transparent"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="font-display text-4xl md:text-5xl font-bold mb-4">Stream <span class="text-glitch-cyan">the Signal</span></h2>
                <p class="text-gray-400">Find my music on your favorite platform</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($platforms as $p): ?>
                <a href="<?php echo htmlspecialchars($p['url']); ?>" target="_blank" rel="noopener noreferrer" class="group relative bg-glitch-surface p-6 rounded-2xl border border-white/5 transition-all duration-300 hover:transform hover:scale-110 <?php echo $platformHover[$p['hover']] ?? $platformHover['green']; ?> flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br <?php echo htmlspecialchars($p['gradient']); ?> flex items-center justify-center text-white shadow-lg group-hover:rotate-12 transition-transform">
                        <?php if (!empty($p['icon_data'])): ?>
                        <img src="<?php echo htmlspecialchars($p['icon_data']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?> icon" class="w-8 h-8 object-contain" width="32" height="32">
                        <?php elseif (!empty($p['icon_slug'])): ?>
                        <img src="icons/<?php echo htmlspecialchars($p['icon_slug']); ?>.svg" alt="<?php echo htmlspecialchars($p['name']); ?> icon" class="w-8 h-8 object-contain" width="32" height="32">
                        <?php else: ?>
                        <i data-lucide="<?php echo htmlspecialchars($p['icon']); ?>" class="w-8 h-8"></i>
                        <?php endif; ?>
                    </div>
                    <span class="font-bold text-gray-300 transition-colors"><?php echo htmlspecialchars($p['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-12 text-center">
                <p class="text-gray-500 text-sm">Also available on NetEase, Saavn, Qobuz, Tiktok, Instagram and 25+ social media and streaming platforms worldwide</p>
            </div>
        </div>
    </section>

    <!-- Newsletter / Contact -->
    <section id="contact" class="py-24 bg-glitch-surface/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-display text-4xl md:text-5xl font-bold mb-4">Be Part of the <span class="text-glitch-magenta">Malfunction</span></h2>
            <p class="text-gray-400 mb-8">Exclusive updates, first listens, and unseen moments.</p>

            <div id="subscribe-area">
            <?php
            $subscribeStatus = $_GET['subscribe'] ?? '';
            if ($subscribeStatus === 'success'):
            ?>
            <p class="mb-6 px-4 py-3 rounded-full bg-glitch-cyan/20 text-glitch-cyan border border-glitch-cyan/40 max-w-lg mx-auto">Thanks for subscribing!</p>
            <?php elseif ($subscribeStatus === 'invalid'): ?>
            <p class="mb-6 px-4 py-3 rounded-full bg-red-500/20 text-red-300 border border-red-500/40 max-w-lg mx-auto">Please enter a valid email address.</p>
            <?php elseif ($subscribeStatus === 'error'): ?>
            <p class="mb-6 px-4 py-3 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 max-w-lg mx-auto">Subscription is temporarily unavailable. Try again later.</p>
            <?php endif; ?>
            
            <form id="subscribe-form" action="subscribe.php" method="post" class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto mb-12">
                <input type="email" name="email" placeholder="Enter your email" required class="flex-1 px-6 py-4 bg-glitch-dark border border-white/10 rounded-full focus:outline-none focus:border-glitch-cyan focus:ring-2 focus:ring-glitch-cyan/20 text-white placeholder-gray-500 transition-all">
                <button type="submit" id="subscribe-btn" class="px-8 py-4 bg-glitch-cyan text-glitch-dark font-bold rounded-full hover:bg-white transition-colors duration-300 flex items-center justify-center gap-2">
                    Subscribe
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
            </div>

            <div id="subscribe-thankyou" class="hidden max-w-lg mx-auto mb-12">
                <div class="px-8 py-10 rounded-2xl border border-glitch-cyan/30 bg-glitch-cyan/5">
                    <i data-lucide="check-circle" class="w-16 h-16 text-glitch-cyan mx-auto mb-4"></i>
                    <h3 class="font-display text-2xl font-bold text-white mb-2">You're in.</h3>
                    <p class="text-gray-400">Thanks for subscribing. You'll hear from us soon.</p>
                </div>
            </div>

            <div id="subscribe-error" class="hidden mb-6"></div>
            
            <div class="flex justify-center gap-6">
                <!-- <a href="#" class="w-12 h-12 rounded-full bg-glitch-dark border border-white/10 flex items-center justify-center text-gray-400 hover:text-glitch-cyan hover:border-glitch-cyan hover:shadow-[0_0_20px_rgba(0,243,255,0.3)] transition-all duration-300">
                    <i data-lucide="instagram" class="w-5 h-5"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-full bg-glitch-dark border border-white/10 flex items-center justify-center text-gray-400 hover:text-glitch-cyan hover:border-glitch-cyan hover:shadow-[0_0_20px_rgba(0,243,255,0.3)] transition-all duration-300">
                    <i data-lucide="twitter" class="w-5 h-5"></i>
                </a> -->
                <a href="https://www.facebook.com/people/Zero-Glitch/61581392979788/" class="w-12 h-12 rounded-full bg-glitch-dark border border-white/10 flex items-center justify-center text-gray-400 hover:text-glitch-cyan hover:border-glitch-cyan hover:shadow-[0_0_20px_rgba(0,243,255,0.3)] transition-all duration-300">
                    <i data-lucide="facebook" class="w-5 h-5"></i>
                </a>
                <a href="https://www.youtube.com/@glitch015" class="w-12 h-12 rounded-full bg-glitch-dark border border-white/10 flex items-center justify-center text-gray-400 hover:text-glitch-cyan hover:border-glitch-cyan hover:shadow-[0_0_20px_rgba(0,243,255,0.3)] transition-all duration-300">
                    <i data-lucide="youtube" class="w-5 h-5"></i>
                </a>
                <a href="mailto:iam@0glitch.com" class="w-12 h-12 rounded-full bg-glitch-dark border border-white/10 flex items-center justify-center text-gray-400 hover:text-glitch-cyan hover:border-glitch-cyan hover:shadow-[0_0_20px_rgba(0,243,255,0.3)] transition-all duration-300">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 border-t border-white/10 bg-glitch-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="font-display font-bold text-xl text-white">ZERO GLITCH</span>
                <span class="text-gray-600">|</span>
                <span class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> All Rights Reserved</span>
            </div>
            <div class="flex gap-6 text-sm text-gray-500">
                <a href="#" class="hover:text-glitch-cyan transition-colors">Privacy</a>
                <a href="#" class="hover:text-glitch-cyan transition-colors">Terms</a>
                <a href="#" class="hover:text-glitch-cyan transition-colors">Press Kit</a>
            </div>
        </div>
    </footer>

    <!-- Track player modal (YouTube / Spotify embed) - single track from Featured Tracks -->
    <div id="track-player-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm" aria-modal="true" aria-labelledby="track-player-title">
        <div class="relative w-full max-w-lg bg-glitch-surface rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            <button type="button" id="track-player-close" class="absolute top-3 right-3 z-10 w-10 h-10 rounded-full bg-black/50 border border-white/20 flex items-center justify-center text-white hover:bg-white/10 transition-colors" aria-label="Close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="p-4 pb-2">
                <h2 id="track-player-title" class="font-display text-xl font-bold text-white pr-10"></h2>
                <p id="track-player-album" class="text-gray-400 text-sm"></p>
            </div>
            <div class="flex border-b border-white/10" id="track-player-tabs">
                <button type="button" data-player-tab="youtube" class="flex-1 py-3 text-sm font-medium text-gray-400 hover:text-white border-b-2 border-transparent data-[active]:text-glitch-cyan data-[active]:border-glitch-cyan transition-colors hidden">YouTube</button>
                <button type="button" data-player-tab="spotify" class="flex-1 py-3 text-sm font-medium text-gray-400 hover:text-white border-b-2 border-transparent data-[active]:text-glitch-cyan data-[active]:border-glitch-cyan transition-colors hidden">Spotify</button>
            </div>
            <div class="aspect-video bg-black">
                <iframe id="track-player-iframe" class="w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Track player"></iframe>
            </div>
            <div class="p-3 text-center text-xs text-gray-400">
                <span id="track-player-links-wrap" class="hidden">Open in <a id="track-player-link-yt" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">YouTube Music</a><span id="track-player-sep1" class="text-gray-500 mx-1 hidden">•</span><a id="track-player-link-sp" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">Spotify</a><span id="track-player-sep2" class="text-gray-500 mx-1 hidden">•</span><a id="track-player-link-am" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">Amazon Music</a><span id="track-player-sep3" class="text-gray-500 mx-1 hidden">•</span><a id="track-player-link-apple" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">Apple Music</a></span>
            </div>
        </div>
    </div>

    <!-- Album playlist modal (Latest Albums) - embed + track list -->
    <div id="album-playlist-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm" aria-modal="true" aria-labelledby="album-playlist-title">
        <div class="relative w-full max-w-4xl max-h-[90vh] bg-glitch-surface rounded-2xl border border-white/10 shadow-2xl overflow-hidden flex flex-col">
            <button type="button" id="album-playlist-close" class="absolute top-3 right-3 z-10 w-10 h-10 rounded-full bg-black/50 border border-white/20 flex items-center justify-center text-white hover:bg-white/10 transition-colors" aria-label="Close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="p-4 pb-2 pr-14">
                <h2 id="album-playlist-title" class="font-display text-xl font-bold text-white"></h2>
                <p id="album-playlist-subtitle" class="text-gray-400 text-sm"></p>
            </div>
            <div class="flex border-b border-white/10" id="album-playlist-tabs">
                <button type="button" data-album-tab="youtube" class="flex-1 py-3 text-sm font-medium text-gray-400 hover:text-white border-b-2 border-transparent data-[active]:text-glitch-cyan data-[active]:border-glitch-cyan transition-colors hidden">YouTube</button>
                <button type="button" data-album-tab="spotify" class="flex-1 py-3 text-sm font-medium text-gray-400 hover:text-white border-b-2 border-transparent data-[active]:text-glitch-cyan data-[active]:border-glitch-cyan transition-colors hidden">Spotify</button>
            </div>
            <div class="flex flex-col md:flex-row flex-1 min-h-0">
                <div class="flex-1 min-w-0 bg-black flex items-center justify-center relative">
                    <div id="album-youtube-player-wrap" class="absolute inset-0 hidden w-full h-full min-h-[280px]"></div>
                    <iframe id="album-playlist-iframe" class="w-full aspect-video md:aspect-auto md:h-full md:min-h-[320px]" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Album playlist"></iframe>
                </div>
                <div class="w-full md:w-72 flex-shrink-0 border-t md:border-t-0 md:border-l border-white/10 flex flex-col bg-glitch-dark/50">
                    <div class="px-3 py-2 border-b border-white/10 flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4 text-glitch-cyan flex-shrink-0"></i>
                        <span class="text-xs font-semibold text-gray-300 uppercase tracking-wider">Track list</span>
                    </div>
                    <ul id="album-playlist-tracks" class="flex-1 overflow-y-auto py-2 text-sm text-gray-300 list-none divide-y divide-white/5">
                        <!-- filled by JS -->
                    </ul>
                </div>
            </div>
            <div class="p-3 text-center text-xs text-gray-400 border-t border-white/10">
                <span id="album-playlist-links-wrap" class="hidden">Open in <a id="album-playlist-link-yt" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">YouTube Music</a><span id="album-playlist-sep1" class="text-gray-500 mx-1 hidden">•</span><a id="album-playlist-link-sp" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">Spotify</a><span id="album-playlist-sep2" class="text-gray-500 mx-1 hidden">•</span><a id="album-playlist-link-am" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">Amazon Music</a><span id="album-playlist-sep3" class="text-gray-500 mx-1 hidden">•</span><a id="album-playlist-link-apple" href="#" target="_blank" rel="noopener noreferrer" class="hover:text-glitch-cyan transition-colors hidden">Apple Music</a></span>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://www.youtube.com/iframe_api" async></script>
    <script>
        lucide.createIcons();
    </script>
    <script>
    (function() {
        var modal = document.getElementById('track-player-modal');
        var iframe = document.getElementById('track-player-iframe');
        var closeBtn = document.getElementById('track-player-close');
        var titleEl = document.getElementById('track-player-title');
        var albumEl = document.getElementById('track-player-album');
        var linkYt = document.getElementById('track-player-link-yt');
        var linkSp = document.getElementById('track-player-link-sp');
        var linkAm = document.getElementById('track-player-link-am');
        var linkApple = document.getElementById('track-player-link-apple');
        var tabs = document.querySelectorAll('[data-player-tab]');

        function openModal(card) {
            var ytEmbed = card.getAttribute('data-youtube-embed') || '';
            var spEmbed = card.getAttribute('data-spotify-embed') || '';
            var firstUrl = ytEmbed || spEmbed;
            if (!firstUrl) {
                var ext = card.getAttribute('data-external-link');
                if (ext) window.open(ext, '_blank');
                return;
            }
            titleEl.textContent = card.getAttribute('data-title') || '';
            albumEl.textContent = card.getAttribute('data-album') || '';
            var hasYtLink = !!card.getAttribute('data-youtube-link');
            var hasSpLink = !!card.getAttribute('data-spotify-link');
            var hasAmLink = !!card.getAttribute('data-amazon-link');
            var hasAppleLink = !!card.getAttribute('data-apple-link');
            linkYt.href = card.getAttribute('data-youtube-link') || '#';
            linkSp.href = card.getAttribute('data-spotify-link') || '#';
            linkAm.href = card.getAttribute('data-amazon-link') || '#';
            linkApple.href = card.getAttribute('data-apple-link') || '#';
            linkYt.classList.toggle('hidden', !hasYtLink);
            linkSp.classList.toggle('hidden', !hasSpLink);
            linkAm.classList.toggle('hidden', !hasAmLink);
            linkApple.classList.toggle('hidden', !hasAppleLink);
            document.getElementById('track-player-sep1').classList.toggle('hidden', !(hasYtLink && hasSpLink));
            document.getElementById('track-player-sep2').classList.toggle('hidden', !(hasSpLink && hasAmLink));
            document.getElementById('track-player-sep3').classList.toggle('hidden', !(hasAmLink && hasAppleLink));
            document.getElementById('track-player-links-wrap').classList.toggle('hidden', !(hasYtLink || hasSpLink || hasAmLink || hasAppleLink));
            document.querySelector('[data-player-tab="youtube"]').classList.toggle('hidden', !ytEmbed);
            document.querySelector('[data-player-tab="spotify"]').classList.toggle('hidden', !spEmbed);
            document.getElementById('track-player-tabs').classList.toggle('hidden', !ytEmbed && !spEmbed);
            var useYoutubeFirst = !!ytEmbed;
            if (useYoutubeFirst) {
                iframe.src = ytEmbed;
                document.querySelector('[data-player-tab="youtube"]').setAttribute('data-active', '');
                document.querySelector('[data-player-tab="spotify"]').removeAttribute('data-active');
            } else {
                iframe.src = spEmbed;
                document.querySelector('[data-player-tab="spotify"]').setAttribute('data-active', '');
                document.querySelector('[data-player-tab="youtube"]').removeAttribute('data-active');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            iframe.src = 'about:blank';
        }

        tabs.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var card = modal._trackCard;
                if (!card) return;
                var yt = card.getAttribute('data-youtube-embed');
                var sp = card.getAttribute('data-spotify-embed');
                var name = this.getAttribute('data-player-tab');
                if (name === 'youtube' && yt) { iframe.src = yt; document.querySelectorAll('[data-player-tab]').forEach(function(t) { t.removeAttribute('data-active'); }); this.setAttribute('data-active', ''); }
                else if (name === 'spotify' && sp) { iframe.src = sp; document.querySelectorAll('[data-player-tab]').forEach(function(t) { t.removeAttribute('data-active'); }); this.setAttribute('data-active', ''); }
            });
        });

        document.querySelectorAll('[data-track-card]').forEach(function(card) {
            card.addEventListener('click', function() {
                var ytEmbed = card.getAttribute('data-youtube-embed');
                var spEmbed = card.getAttribute('data-spotify-embed');
                if (ytEmbed || spEmbed) {
                    modal._trackCard = card;
                    openModal(card);
                } else {
                    var ext = card.getAttribute('data-external-link');
                    if (ext) window.open(ext, '_blank');
                }
            });
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.click(); }
            });
        });

        // Latest Albums: open album-playlist modal (embed + track list, clickable tracks)
        var albumModal = document.getElementById('album-playlist-modal');
        var albumIframe = document.getElementById('album-playlist-iframe');
        var albumYoutubeWrap = document.getElementById('album-youtube-player-wrap');
        var albumCloseBtn = document.getElementById('album-playlist-close');
        var albumTitleEl = document.getElementById('album-playlist-title');
        var albumSubtitleEl = document.getElementById('album-playlist-subtitle');
        var albumTracksList = document.getElementById('album-playlist-tracks');
        var albumTabYt = document.querySelector('[data-album-tab="youtube"]');
        var albumTabSp = document.querySelector('[data-album-tab="spotify"]');
        var albumTabsWrap = document.getElementById('album-playlist-tabs');

        function destroyAlbumYtPlayer() {
            if (albumModal._ytPlayer && albumModal._ytPlayer.destroy) {
                try { albumModal._ytPlayer.destroy(); } catch (e) {}
                albumModal._ytPlayer = null;
            }
            albumYoutubeWrap.innerHTML = '';
            albumYoutubeWrap.classList.add('hidden');
        }

        function createAlbumYtPlayer(listId) {
            albumYoutubeWrap.innerHTML = '<div id="album-yt-player-inner"></div>';
            if (!window.YT || !window.YT.Player) return;
            new window.YT.Player('album-yt-player-inner', {
                width: '100%',
                height: '100%',
                playerVars: { list: listId, autoplay: 1 },
                events: { onReady: function(e) { albumModal._ytPlayer = e.target; } }
            });
        }

        window.onYouTubeIframeAPIReady = (function(prev) {
            return function() {
                if (prev) prev();
                if (window._albumPendingYtListId && window._albumPendingYtWrap) {
                    createAlbumYtPlayer(window._albumPendingYtListId);
                    window._albumPendingYtListId = null;
                    window._albumPendingYtWrap = null;
                }
            };
        })(window.onYouTubeIframeAPIReady);

        function openAlbumModal(btn) {
            var ytEmbed = btn.getAttribute('data-youtube-embed') || '';
            var spEmbed = btn.getAttribute('data-spotify-embed') || '';
            if (!ytEmbed && !spEmbed) return;
            albumModal._albumCard = btn;
            var ytListId = btn.getAttribute('data-youtube-list-id') || '';
            var trackSpotifyEmbeds = [];
            try {
                var je = btn.getAttribute('data-track-spotify-embeds');
                if (je) trackSpotifyEmbeds = JSON.parse(je);
            } catch (e) {}
            albumModal._trackSpotifyEmbeds = trackSpotifyEmbeds;

            albumTitleEl.textContent = btn.getAttribute('data-title') || '';
            albumSubtitleEl.textContent = btn.getAttribute('data-album') || '';
            var tracksJson = btn.getAttribute('data-tracks');
            var tracks = [];
            try {
                if (tracksJson) tracks = JSON.parse(tracksJson);
            } catch (e) {}
            albumTracksList.innerHTML = '';
            tracks.forEach(function(name, i) {
                if (!name) return;
                var li = document.createElement('li');
                li.className = 'px-3 py-2 flex items-center gap-2 cursor-pointer hover:bg-white/10 rounded transition-colors album-track-item';
                li.setAttribute('data-index', String(i));
                li.setAttribute('role', 'button');
                li.setAttribute('tabindex', '0');
                var num = document.createElement('span');
                num.className = 'text-gray-500 tabular-nums w-6 flex-shrink-0';
                num.textContent = (i + 1) + '.';
                var title = document.createElement('span');
                title.className = 'truncate';
                title.textContent = name;
                li.appendChild(num);
                li.appendChild(title);
                albumTracksList.appendChild(li);
            });
            var hasYt = !!ytEmbed;
            var hasSp = !!spEmbed;
            document.getElementById('album-playlist-link-yt').href = btn.getAttribute('data-youtube-link') || '#';
            document.getElementById('album-playlist-link-sp').href = btn.getAttribute('data-spotify-link') || '#';
            document.getElementById('album-playlist-link-am').href = btn.getAttribute('data-amazon-link') || '#';
            document.getElementById('album-playlist-link-apple').href = btn.getAttribute('data-apple-link') || '#';
            document.getElementById('album-playlist-link-yt').classList.toggle('hidden', !btn.getAttribute('data-youtube-link'));
            document.getElementById('album-playlist-link-sp').classList.toggle('hidden', !btn.getAttribute('data-spotify-link'));
            document.getElementById('album-playlist-link-am').classList.toggle('hidden', !btn.getAttribute('data-amazon-link'));
            document.getElementById('album-playlist-link-apple').classList.toggle('hidden', !btn.getAttribute('data-apple-link'));
            document.getElementById('album-playlist-sep1').classList.toggle('hidden', !(hasYt && hasSp));
            document.getElementById('album-playlist-sep2').classList.toggle('hidden', !(hasSp && btn.getAttribute('data-amazon-link')));
            document.getElementById('album-playlist-sep3').classList.toggle('hidden', !(btn.getAttribute('data-amazon-link') && btn.getAttribute('data-apple-link')));
            document.getElementById('album-playlist-links-wrap').classList.toggle('hidden', !(hasYt || hasSp || btn.getAttribute('data-amazon-link') || btn.getAttribute('data-apple-link')));
            albumTabYt.classList.toggle('hidden', !ytEmbed);
            albumTabSp.classList.toggle('hidden', !spEmbed);
            albumTabsWrap.classList.toggle('hidden', !ytEmbed && !spEmbed);

            if (ytEmbed && ytListId) {
                albumIframe.classList.add('hidden');
                albumIframe.src = 'about:blank';
                albumYoutubeWrap.classList.remove('hidden');
                if (window.YT && window.YT.Player) {
                    createAlbumYtPlayer(ytListId);
                } else {
                    window._albumPendingYtListId = ytListId;
                    window._albumPendingYtWrap = albumYoutubeWrap;
                }
                albumModal._albumCurrentTab = 'youtube';
                albumTracksList.classList.remove('album-tracks-display-only');
                albumTabYt.setAttribute('data-active', '');
                albumTabSp.removeAttribute('data-active');
            } else {
                destroyAlbumYtPlayer();
                albumIframe.classList.remove('hidden');
                albumIframe.src = spEmbed;
                albumModal._albumCurrentTab = 'spotify';
                albumTracksList.classList.add('album-tracks-display-only');
                albumTabSp.setAttribute('data-active', '');
                albumTabYt.removeAttribute('data-active');
            }
            albumModal.classList.remove('hidden');
            albumModal.classList.add('flex');
            if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }

        function setAlbumTrackActive(index) {
            albumTracksList.querySelectorAll('.album-track-item').forEach(function(el) {
                el.classList.toggle('bg-glitch-cyan/20', parseInt(el.getAttribute('data-index'), 10) === index);
                el.classList.toggle('text-glitch-cyan', parseInt(el.getAttribute('data-index'), 10) === index);
            });
        }

        albumTracksList.addEventListener('click', function(e) {
            if (albumModal._albumCurrentTab !== 'youtube') return;
            var item = e.target.closest('.album-track-item');
            if (!item || !albumModal._albumCard) return;
            var index = parseInt(item.getAttribute('data-index'), 10);
            if (isNaN(index)) return;
            setAlbumTrackActive(index);
            if (albumModal._ytPlayer && albumModal._ytPlayer.playVideoAt) {
                albumModal._ytPlayer.playVideoAt(index);
            }
        });
        albumTracksList.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var item = e.target.closest('.album-track-item');
            if (!item) return;
            e.preventDefault();
            item.click();
        });

        function closeAlbumModal() {
            albumModal.classList.add('hidden');
            albumModal.classList.remove('flex');
            albumIframe.src = 'about:blank';
            albumIframe.classList.remove('hidden');
            destroyAlbumYtPlayer();
        }

        document.querySelectorAll('[data-album-play-card]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var ytEmbed = btn.getAttribute('data-youtube-embed');
                var spEmbed = btn.getAttribute('data-spotify-embed');
                if (ytEmbed || spEmbed) openAlbumModal(btn);
            });
        });

        albumCloseBtn.addEventListener('click', closeAlbumModal);
        albumModal.addEventListener('click', function(e) {
            if (e.target === albumModal) closeAlbumModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && albumModal.classList.contains('flex')) closeAlbumModal();
        });

        document.querySelectorAll('[data-album-tab]').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var card = albumModal._albumCard;
                if (!card) return;
                var yt = card.getAttribute('data-youtube-embed');
                var sp = card.getAttribute('data-spotify-embed');
                var ytListId = card.getAttribute('data-youtube-list-id') || '';
                var name = this.getAttribute('data-album-tab');
                if (name === 'youtube' && yt && ytListId) {
                    destroyAlbumYtPlayer();
                    albumIframe.classList.add('hidden');
                    albumIframe.src = 'about:blank';
                    albumYoutubeWrap.classList.remove('hidden');
                    if (window.YT && window.YT.Player) {
                        createAlbumYtPlayer(ytListId);
                    } else {
                        window._albumPendingYtListId = ytListId;
                        window._albumPendingYtWrap = albumYoutubeWrap;
                    }
                    albumModal._albumCurrentTab = 'youtube';
                    albumTracksList.classList.remove('album-tracks-display-only');
                    document.querySelectorAll('[data-album-tab]').forEach(function(t) { t.removeAttribute('data-active'); });
                    this.setAttribute('data-active', '');
                } else if (name === 'spotify' && sp) {
                    destroyAlbumYtPlayer();
                    albumIframe.classList.remove('hidden');
                    albumIframe.src = sp;
                    albumModal._albumCurrentTab = 'spotify';
                    albumTracksList.classList.add('album-tracks-display-only');
                    document.querySelectorAll('[data-album-tab]').forEach(function(t) { t.removeAttribute('data-active'); });
                    this.setAttribute('data-active', '');
                }
            });
        });

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Escape') return;
            if (albumModal.classList.contains('flex')) closeAlbumModal();
            else if (modal.classList.contains('flex')) closeModal();
        });

        // About "Breaking the Silence": play video (muted, loop) on hover over image; show image until video ready, then show video
        var aboutMedia = document.querySelector('.about-hero-media');
        if (aboutMedia) {
            var aboutVideo = aboutMedia.querySelector('.about-hero-video');
            var aboutPoster = aboutMedia.querySelector('.about-hero-poster');
            var aboutHovering = false;
            function showVideo() {
                if (!aboutHovering) return;
                aboutVideo.classList.remove('opacity-0');
                if (aboutPoster) aboutPoster.classList.add('opacity-0');
            }
            function hideVideo() {
                aboutVideo.pause();
                aboutVideo.classList.add('opacity-0');
                if (aboutPoster) aboutPoster.classList.remove('opacity-0');
            }
            aboutVideo.addEventListener('canplay', showVideo, { once: false });
            aboutMedia.addEventListener('mouseenter', function() {
                aboutHovering = true;
                aboutVideo.play().catch(function() {});
                if (aboutVideo.readyState >= 2) showVideo();
            });
            aboutMedia.addEventListener('mouseleave', function() {
                aboutHovering = false;
                hideVideo();
            });
        }

        // Track card thumbnails: show video when it can play (loop, muted); keep image visible until then
        document.querySelectorAll('.track-card-video').forEach(function(video) {
            function showVideo() {
                video.classList.remove('opacity-0');
                var card = video.closest('[data-track-card]');
                if (card) {
                    var poster = card.querySelector('.track-card-poster');
                    if (poster) poster.classList.add('opacity-0');
                }
            }
            if (video.readyState >= 2) showVideo();
            else video.addEventListener('canplay', showVideo, { once: true });
            video.addEventListener('error', function() {
                var card = video.closest('[data-track-card]');
                if (card) {
                    var poster = card.querySelector('.track-card-poster');
                    if (poster) poster.classList.remove('opacity-0');
                }
            }, { once: true });
        });
    })();
    </script>
</body>
</html>
