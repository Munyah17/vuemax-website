<?php
require_once __DIR__ . '/inc.php';
require_admin();

$images = [];
$noDb   = ($pdo === null);
if ($pdo) {
    try {
        $images = $pdo->query('SELECT img_key, label, page, path, updated_at FROM site_images ORDER BY page, img_key')->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}

// Group by page
$groups = [];
foreach ($images as $im) { $groups[$im['page'] ?: 'Other'][] = $im; }

$flash = '';
if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Image Manager — Vuemax Admin</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:#F4F1EC;color:#0A1D33;}
  header{background:#0A1D33;color:#fff;padding:14px 24px;display:flex;align-items:center;gap:14px;position:sticky;top:0;z-index:10;}
  header .mark{width:32px;height:32px;border-radius:8px;background:#F2A33C;color:#0A1D33;font-weight:800;display:flex;align-items:center;justify-content:center;font-size:18px;}
  header h1{font-size:16px;font-weight:700;flex:1;}
  header a{color:#F2A33C;font-size:12.5px;text-decoration:none;margin-left:14px;}
  header a:hover{text-decoration:underline;}
  main{max-width:1100px;margin:0 auto;padding:24px 20px 60px;}
  .flash{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;}
  .flash.ok{background:#ECFDF5;border:1px solid #A7F3D0;color:#047857;}
  .flash.err{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C;}
  .warn{background:#FFFBEB;border:1px solid #FDE68A;color:#92400E;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;}
  h2{font-size:15px;font-weight:700;margin:26px 0 12px;padding-bottom:8px;border-bottom:2px solid #E5E0D8;}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;}
  .img-card{background:#fff;border:1px solid #E7E2DA;border-radius:12px;overflow:hidden;}
  .img-card .preview{height:120px;background:#E9E4DC center/cover no-repeat;display:flex;align-items:center;justify-content:center;color:#9CA3AF;font-size:11px;}
  .img-card .body{padding:12px 14px;}
  .img-card h3{font-size:13px;font-weight:600;margin-bottom:2px;}
  .img-card .key{font-size:10.5px;color:#9CA3AF;font-family:Consolas,monospace;margin-bottom:10px;}
  .img-card form{display:flex;gap:8px;align-items:center;}
  .img-card input[type=file]{font-size:11px;width:100%;color:#6B7280;}
  .img-card button{flex-shrink:0;padding:7px 12px;background:#0A1D33;color:#fff;border:0;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;}
  .img-card button:hover{background:#14355C;}
  .custom{background:#F2A33C22;border:1px dashed #F2A33C;}
  .hint{font-size:12px;color:#6B7280;margin-top:6px;}
</style>
</head>
<body>
<header>
  <div class="mark">V</div>
  <h1>Vuemax Admin — Image Manager</h1>
  <a href="../index.php" target="_blank">View Site</a>
  <a href="logout.php">Sign Out</a>
</header>
<main>

<?php if ($noDb): ?>
  <div class="warn"><strong>Database not connected.</strong> Set the credentials in <code>includes/config.php</code> and import <code>sql/schema.sql</code>. Image management requires MySQL.</div>
<?php endif; ?>

<?php if ($flash):
  [$kind, $val] = explode(':', $flash, 2);
  if ($kind === 'ok'): ?>
    <div class="flash ok">Image updated: <strong><?= e($val) ?></strong></div>
  <?php else:
    $msgs = ['db' => 'Database error', 'nofile' => 'No file received', 'toobig' => 'File too large (max 4 MB)', 'type' => 'Only JPG, PNG, WebP or GIF allowed', 'move' => 'Could not save the file']; ?>
    <div class="flash err"><?= e($msgs[$val] ?? 'Upload failed') ?></div>
  <?php endif; ?>
<?php endif; ?>

<p class="hint">Upload a replacement for any image slot below. JPG, PNG, WebP or GIF, max 4 MB. Changes apply site-wide immediately.</p>

<?php foreach ($groups as $page => $items): ?>
  <h2><?= e($page) ?></h2>
  <div class="grid">
    <?php foreach ($items as $im):
      $isUpload = $im['path'] && strpos($im['path'], 'assets/img/uploads/') === 0;
      $thumb = $im['path'] ? '../' . ltrim($im['path'], '/') : '';
      if ($im['path'] && strpos($im['path'], 'http') === 0) $thumb = $im['path'];
    ?>
    <div class="img-card" id="<?= e($im['img_key']) ?>">
      <div class="preview" <?= $thumb ? 'style="background-image:url(\'' . e($thumb) . '\')"' : '' ?>><?= $thumb ? '' : 'no image' ?></div>
      <div class="body">
        <h3><?= e($im['label']) ?></h3>
        <div class="key"><?= e($im['img_key']) ?><?= $isUpload ? ' · custom upload' : '' ?></div>
        <form method="post" action="upload.php" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="key" value="<?= e($im['img_key']) ?>">
          <input type="file" name="image" accept="image/*" required>
          <button type="submit">Replace</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>

</main>
</body>
</html>
