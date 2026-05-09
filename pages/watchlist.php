<?php
$pageTitle = 'My Race Planner — F1 Planner';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();
$db = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

$limit = (int)($_GET['limit'] ?? 50);
if ($limit < 1 || $limit > 100) {
    $limit = 50;
}

$sql = '
    SELECT ur.id AS link_id,
           ur.is_favorite,
           ur.note_text,
           r.*
      FROM user_races ur
      JOIN races r ON r.id = ur.race_id
     WHERE ur.user_id = ?
     ORDER BY r.race_date ASC
     LIMIT ?
';
$stmt = $db->prepare($sql);
$stmt->execute([$user_id, $limit]);
$items = $stmt->fetchAll();

$countStmt = $db->prepare('SELECT COUNT(*) FROM user_races WHERE user_id = ?');
$countStmt->execute([$user_id]);
$total = (int)$countStmt->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>My Race Planner</h1>
    <p class="subtitle"><?= $total ?> races in your planner</p>
</div>

<?php if (!$items): ?>
    <div class="no-results">
        <p>You do not have any planner races yet.</p>
        <a href="/races.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Races</a>
    </div>
<?php else: ?>
    <div class="races-grid">
        <?php foreach ($items as $i => $race): ?>
            <div class="race-card">
                <div class="race-card-header">
                    <div>
                        <div class="race-round">Round <?= $i + 1 ?></div>
                        <div style="color:var(--text-muted);font-size:0.8rem;margin-top:2px;">
                            <?= date('M d, Y', strtotime($race['race_date'])) ?>
                        </div>
                    </div>
                    <div class="race-flag"><?= htmlspecialchars($race['flag_emoji']) ?></div>
                </div>
                <div class="race-card-body">
                    <div class="race-gp-name"><?= htmlspecialchars($race['grand_prix_name']) ?></div>
                    <div class="race-circuit">📍 <?= htmlspecialchars($race['circuit_name']) ?></div>
                    <div class="race-circuit">🌍 <?= htmlspecialchars($race['country']) ?></div>
                    <?php if (!empty($race['note_text'])): ?>
                        <div class="race-circuit" style="margin-top:0.5rem;">
                            📝 <?= htmlspecialchars($race['note_text']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="race-card-footer">
                    <span class="badge badge-<?= $race['status'] ?>"><?= ucfirst($race['status']) ?></span>
                    <div class="d-flex gap-1 align-center">
                        <a href="/pages/race.php?id=<?= (int)$race['id'] ?>" class="btn btn-primary btn-sm">View →</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="margin-top: 2rem; text-align: center;">
    <a href="/races.php" class="btn btn-outline">← Back to All Races</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
