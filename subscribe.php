<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#contact');
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    || !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

$email = trim($_POST['email'] ?? '');
$redirect = 'index.php#contact';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'invalid']);
        exit;
    }
    header('Location: ' . $redirect . '?subscribe=invalid');
    exit;
}

$csvFile = __DIR__ . '/emails.csv';
$newFile = !file_exists($csvFile);
$fp = @fopen($csvFile, 'a');
if ($fp === false) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'error']);
        exit;
    }
    header('Location: ' . $redirect . '?subscribe=error');
    exit;
}
if ($newFile) {
    fputcsv($fp, ['email', 'subscribed_at']);
}
fputcsv($fp, [$email, date('Y-m-d H:i:s')]);
fclose($fp);

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}
header('Location: ' . $redirect . '?subscribe=success');
exit;
