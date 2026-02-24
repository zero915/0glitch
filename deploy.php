<?php
// deploy.php

// Optional: restrict access by a secret key
$secret = 'SYSTEM_UPGRADE_REBOOT'; 
$headers = getallheaders();
if (!isset($headers['X-Hub-Signature']) || empty($headers['X-Hub-Signature'])) {
    http_response_code(403);
    die('No signature');
}

// Optional: verify payload with secret
$payload = file_get_contents('php://input');
$hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
if (!hash_equals($hash, $headers['X-Hub-Signature'])) {
    http_response_code(403);
    die('Invalid signature');
}

// Navigate to the repo
$repo_dir = '/home/pecjocom/public_html_0glitch';
chdir($repo_dir);

// Pull latest changes
$output = shell_exec('git reset --hard 2>&1 && git pull origin main 2>&1');
echo "<pre>$output</pre>";
?>
