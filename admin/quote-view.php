<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);
$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id || !$pdo) { header('Location: quotes.php'); exit; }

$STATUSES = ['new','sent','accepted','archived'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (($_POST['act'] ?? '') === 'status') {
            $st = $_POST['status'] ?? '';
            if (in_array($st, $STATUSES, true)) {
                $pdo->prepare('UPDATE quotes SET status=? WHERE id=?')->execute([$st, $id]);
            }
        }
    } catch (Throwable $e) { header('Location: quote-view.php?id=' . $id . '&err=db'); exit; }
    header('Location: quote-view.php?id=' . $id);
    exit;
}

$quote = $items = null;
try {
    $st = $pdo->prepare('SELECT * FROM quotes WHERE id=?');
    $st->execute([$id]); $quote = $st->fetch();
    $st = $pdo->prepare('SELECT * FROM quote_items WHERE quote_id=? ORDER BY sort_order, id');
    $st->execute([$id]); $items = $st->fetchAll();
} catch (Throwable $e) { $noDb = true; }
if (!$quote) { header('Location: quotes.php'); exit; }

$opts = json_decode($quote['options'] ?? '', true) ?: [];
$statusBadge = ['new'=>'primary','sent'=>'info','accepted'=>'success','archived'=>'secondary'];

admin_head('Quote ' . $quote['ref']);
admin_nav('quotes');
?>
<h1 class="mt-4"><code><?= e($quote['ref']) ?></code> <span class="badge bg-<?= $statusBadge[$quote['status']] ?? 'secondary' ?>"><?= e($quote['status']) ?></span></h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="quotes.php">Quotes</a></li>
    <li class="breadcrumb-item active"><?= e($quote['ref']) ?></li>
</ol>

<?php if (isset($_GET['err'])): ?><div class="alert alert-danger py-2">Database error.</div><?php endif; ?>

<div class="row">
    <div class="col-xl-4">
        <!-- CUSTOMER -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-user me-1"></i> Customer</div>
            <div class="card-body">
                <div class="fs-5 fw-bold mb-2"><?= e($quote['customer_name'] ?: 'Not provided') ?></div>
                <div class="mb-1">
                    <i class="fas fa-phone fa-fw me-1 text-muted"></i>
                    <?php if ($quote['customer_phone']): ?><a href="tel:<?= e(preg_replace('/\s+/', '', $quote['customer_phone'])) ?>"><?= e($quote['customer_phone']) ?></a><?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </div>
                <div class="mb-1">
                    <i class="fas fa-envelope fa-fw me-1 text-muted"></i>
                    <?php if ($quote['customer_email']): ?><a href="mailto:<?= e($quote['customer_email']) ?>"><?= e($quote['customer_email']) ?></a><?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </div>
                <?php if ($quote['customer_phone']): ?>
                <a class="btn btn-sm btn-success mt-2" href="https://wa.me/<?= e(preg_replace('/\D+/', '', $quote['customer_phone'])) ?>" target="_blank"><i class="fab fa-whatsapp me-1"></i>WhatsApp customer</a>
                <?php endif; ?>
                <?php if ($quote['customer_notes']): ?>
                <hr>
                <div class="small text-muted mb-1">Customer notes</div>
                <div><?= nl2br(e($quote['customer_notes'])) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- STATUS -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-flag me-1"></i> Status</div>
            <div class="card-body">
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="act" value="status">
                    <select class="form-select form-select-sm" name="status">
                        <?php foreach ($STATUSES as $s): ?>
                        <option value="<?= $s ?>" <?= $quote['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-dark">Update</button>
                </form>
            </div>
        </div>

        <!-- META -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-info-circle me-1"></i> Meta</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Source</span><span class="badge bg-<?= $quote['source'] === 'estimator' ? 'info' : 'secondary' ?>"><?= e($quote['source']) ?></span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Submitted</span><span><?= e($quote['created_at']) ?></span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">IP</span><span><?= e($quote['ip_address'] ?: '—') ?></span></div>
                <div class="text-muted mt-2 text-truncate" title="<?= e($quote['user_agent']) ?>"><?= e($quote['user_agent'] ?: '') ?></div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <!-- PROJECT -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-ruler-combined me-1"></i> Project</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6"><div class="small text-muted">Fence type</div><div class="fw-bold"><?= e($quote['fence_type'] ?: '—') ?></div></div>
                    <div class="col-md-3 col-6"><div class="small text-muted">Perimeter</div><div class="fw-bold"><?= $quote['perimeter'] !== null ? (float)$quote['perimeter'] . ' m' : '—' ?></div></div>
                    <div class="col-md-3 col-6"><div class="small text-muted">Height</div><div class="fw-bold"><?= $quote['height'] !== null ? (float)$quote['height'] . ' m' : '—' ?></div></div>
                    <div class="col-md-3 col-6"><div class="small text-muted">Post spacing</div><div class="fw-bold"><?= $quote['post_spacing'] !== null ? (float)$quote['post_spacing'] . ' m' : '—' ?></div></div>
                    <div class="col-md-3 col-6"><div class="small text-muted">Corners</div><div class="fw-bold"><?= $quote['corners'] !== null ? (int)$quote['corners'] : '—' ?></div></div>
                </div>
                <?php if ($opts): ?>
                <hr>
                <div class="small text-muted mb-1">Options</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach (['topWire'=>'Top wire','gate'=>'Gate','install'=>'Installation','concrete'=>'Concrete posts'] as $k => $lbl): ?>
                    <span class="badge bg-<?= !empty($opts[$k]) ? 'success' : 'light text-dark border' ?>"><?= $lbl ?>: <?= !empty($opts[$k]) ? 'yes' : 'no' ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ITEMS -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-list me-1"></i> Bill of quantities <span class="badge bg-secondary ms-2"><?= count($items) ?></span></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <thead><tr><th class="ps-3">Item</th><th>Spec</th><th class="text-end">Qty</th><th class="text-end">Unit</th><th class="text-end pe-3">Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td class="ps-3"><?= e($it['name']) ?></td>
                            <td class="small text-muted"><?= e($it['spec'] ?: '—') ?></td>
                            <td class="text-end"><?= e($it['qty'] ?: '—') ?></td>
                            <td class="text-end"><?= $it['unit_price'] !== null ? usd($it['unit_price']) : '—' ?></td>
                            <td class="text-end pe-3"><?= $it['total'] !== null ? usd($it['total']) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$items): ?><tr><td class="ps-3 text-muted">No line items stored.</td></tr><?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="4" class="ps-3 text-end">Total</td>
                            <td class="text-end pe-3"><?= $quote['total_usd'] !== null ? usd($quote['total_usd']) : '—' ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
admin_footer();
