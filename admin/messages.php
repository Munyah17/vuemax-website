<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);

// Mark a message as read/replied via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $st = in_array($_POST['status'] ?? '', ['new','read','replied','archived'], true) ? $_POST['status'] : 'read';
    try {
        $pdo->prepare('UPDATE contacts SET status = ? WHERE id = ?')->execute([$st, $id]);
    } catch (Throwable $e) {}
    header('Location: messages.php');
    exit;
}

$rows = [];
if ($pdo) {
    try {
        $rows = $pdo->query('SELECT id, name, phone, email, subject, message, status, created_at FROM contacts ORDER BY id DESC')->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}

admin_head('Messages');
admin_nav('messages');
?>
<h1 class="mt-4">Messages</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Messages</li>
</ol>

<?php if ($noDb): ?>
<div class="alert alert-warning"><strong>Database not connected.</strong> Import <code>sql/schema.sql</code> first.</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-envelope me-1"></i> Contact form submissions <span class="badge bg-secondary ms-2"><?= count($rows) ?></span></div>
    <div class="card-body">
        <?php if (!$rows): ?>
            <p class="text-muted mb-0">No messages yet.</p>
        <?php else: ?>
        <div class="accordion" id="msgList">
        <?php foreach ($rows as $m): ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= $m['status'] === 'new' ? 'fw-bold' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#m<?= (int)$m['id'] ?>">
                        <span class="me-3 text-muted small">#<?= (int)$m['id'] ?></span>
                        <?= e($m['name']) ?>
                        <span class="badge ms-2 <?= ['new'=>'bg-danger','read'=>'bg-secondary','replied'=>'bg-success','archived'=>'bg-dark'][$m['status']] ?? 'bg-secondary' ?>"><?= e($m['status']) ?></span>
                        <span class="ms-auto me-2 small text-muted"><?= e($m['subject'] ?: 'general') ?> · <?= e(substr($m['created_at'], 0, 16)) ?></span>
                    </button>
                </h2>
                <div id="m<?= (int)$m['id'] ?>" class="accordion-collapse collapse <?= $m['status'] === 'new' ? 'show' : '' ?>" data-bs-parent="#msgList">
                    <div class="accordion-body">
                        <p class="mb-1 small text-muted">
                            <i class="fas fa-phone me-1"></i><?= e($m['phone']) ?>
                            <?php if ($m['email']): ?> · <i class="fas fa-envelope me-1"></i><?= e($m['email']) ?><?php endif; ?>
                        </p>
                        <p><?= nl2br(e($m['message'])) ?></p>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                            <button name="status" value="read" class="btn btn-sm btn-outline-secondary">Mark read</button>
                            <button name="status" value="replied" class="btn btn-sm btn-outline-success">Mark replied</button>
                            <button name="status" value="archived" class="btn btn-sm btn-outline-dark">Archive</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
admin_footer();
