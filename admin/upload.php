<?php
require_once __DIR__ . '/inc.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_check();

$key = preg_replace('/[^a-z0-9\-]/', '', $_POST['key'] ?? '');
if (!$key || !$pdo) { header('Location: index.php?err=db'); exit; }

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?err=nofile#' . urlencode($key));
    exit;
}

$f = $_FILES['image'];
if ($f['size'] > 4 * 1024 * 1024) { header('Location: index.php?err=toobig#' . urlencode($key)); exit; }

$info = @getimagesize($f['tmp_name']);
$allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
if (!$info || !isset($allowed[$info[2]])) { header('Location: index.php?err=type#' . urlencode($key)); exit; }

$dir = __DIR__ . '/../assets/img/uploads';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$dest = 'assets/img/uploads/' . $key . '.' . $allowed[$info[2]];

// remove a previous upload for this key (any ext)
foreach (glob($dir . '/' . $key . '.*') as $old) @unlink($old);

if (!move_uploaded_file($f['tmp_name'], __DIR__ . '/../' . $dest)) {
    header('Location: index.php?err=move#' . urlencode($key));
    exit;
}

$st = $pdo->prepare('UPDATE site_images SET path = ? WHERE img_key = ?');
$st->execute([$dest, $key]);

header('Location: index.php?ok=' . urlencode($key) . '#' . urlencode($key));
exit;
