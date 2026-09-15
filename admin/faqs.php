<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    csrf_check();
    $act = $_POST['act'] ?? '';
    try {
        if ($act === 'save') {
            $id   = (int)($_POST['id'] ?? 0);
            $cat  = trim($_POST['category'] ?? 'general');
            $q    = trim($_POST['question'] ?? '');
            $a    = trim($_POST['answer'] ?? '');
            $ord  = (int)($_POST['sort_order'] ?? 0);
            if (!$q || !$a) { header('Location: faqs.php?err=required'); exit; }
            if ($id) {
                $pdo->prepare('UPDATE faqs SET category=?, question=?, answer=?, sort_order=? WHERE id=?')->execute([$cat, $q, $a, $ord, $id]);
            } else {
                $pdo->prepare('INSERT INTO faqs (category, question, answer, sort_order) VALUES (?,?,?,?)')->execute([$cat, $q, $a, $ord]);
            }
            header('Location: faqs.php?ok=saved'); exit;
        }
        if ($act === 'toggle') {
            $pdo->prepare('UPDATE faqs SET is_active = 1 - is_active WHERE id=?')->execute([(int)$_POST['id']]);
            header('Location: faqs.php?ok=toggled'); exit;
        }
        if ($act === 'delete') {
            $pdo->prepare('DELETE FROM faqs WHERE id=?')->execute([(int)$_POST['id']]);
            header('Location: faqs.php?ok=deleted'); exit;
        }
    } catch (Throwable $e) { header('Location: faqs.php?err=db'); exit; }
}

$rows = [];
if ($pdo) {
    try { $rows = $pdo->query('SELECT * FROM faqs ORDER BY category, sort_order, id')->fetchAll(); }
    catch (Throwable $e) { $noDb = true; }
}
$edit = null;
if (isset($_GET['edit']) && $pdo) {
    try {
        $st = $pdo->prepare('SELECT * FROM faqs WHERE id=?'); $st->execute([(int)$_GET['edit']]);
        $edit = $st->fetch();
    } catch (Throwable $e) {}
}
if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];

admin_head('FAQs');
admin_nav('faqs');
?>
<h1 class="mt-4">FAQs</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">FAQs</li>
</ol>

<?php if ($noDb): ?><div class="alert alert-warning"><strong>Database not connected.</strong></div><?php endif; ?>
<?php if ($flash): [$k,$v]=explode(':',$flash,2);
    if ($k==='ok'): ?><div class="alert alert-success py-2">FAQ <?= e($v) ?>.</div>
<?php else: ?><div class="alert alert-danger py-2"><?= e(['required'=>'Question and answer are required.'][$v] ?? 'Error') ?></div><?php endif; ?>
<?php endif; ?>

<div class="row">
    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-question-circle me-1"></i> <?= $edit ? 'Edit FAQ' : 'New FAQ' ?></div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="act" value="save">
                    <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">
                    <div class="mb-2"><label class="small text-muted">Category</label>
                        <select class="form-select" name="category">
                        <?php foreach (['general'=>'General','products'=>'Products','delivery'=>'Delivery','payment'=>'Payment & Pricing','installation'=>'Installation'] as $c=>$l): ?>
                            <option value="<?= $c ?>" <?= $edit && $edit['category']===$c ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                        </select></div>
                    <div class="mb-2"><label class="small text-muted">Question *</label><input class="form-control" name="question" value="<?= e($edit['question'] ?? '') ?>" required></div>
                    <div class="mb-2"><label class="small text-muted">Answer * — lines starting with "- " become bullets</label>
                        <textarea class="form-control" name="answer" rows="6" required><?= e($edit['answer'] ?? '') ?></textarea></div>
                    <div class="mb-3"><label class="small text-muted">Sort order</label><input class="form-control" type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></div>
                    <button class="btn btn-dark w-100"><?= $edit ? 'Save Changes' : 'Add FAQ' ?></button>
                    <?php if ($edit): ?><a class="btn btn-link w-100" href="faqs.php">Cancel</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-list me-1"></i> All FAQs <span class="badge bg-secondary ms-2"><?= count($rows) ?></span></div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-striped table-sm">
                    <thead><tr><th>Category</th><th>Question</th><th>Order</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr class="<?= $r['is_active'] ? '' : 'table-secondary text-muted' ?>">
                        <td><span class="badge bg-secondary"><?= e($r['category']) ?></span></td>
                        <td><?= e($r['question']) ?></td>
                        <td><?= (int)$r['sort_order'] ?></td>
                        <td><span class="badge bg-<?= $r['is_active'] ? 'success' : 'secondary' ?>"><?= $r['is_active'] ? 'live' : 'hidden' ?></span></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-dark" href="faqs.php?edit=<?= (int)$r['id'] ?>"><i class="fas fa-edit"></i></a>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="act" value="toggle">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button class="btn btn-sm btn-outline-<?= $r['is_active'] ? 'warning' : 'success' ?>" title="<?= $r['is_active'] ? 'Hide' : 'Show' ?>"><i class="fas fa-<?= $r['is_active'] ? 'eye-slash' : 'eye' ?>"></i></button>
                            </form>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this FAQ?')">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="act" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
admin_footer(<<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const t = document.getElementById('datatablesSimple');
    if (t) new simpleDatatables.DataTable(t);
});
</script>
HTML);
