<?php
$pageTitle = 'My Favorite Races — F1 Planner';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];

// Get favorite races
$stmt = $db->prepare('
    SELECT r.* FROM races r
    INNER JOIN favorites f ON r.id = f.race_id
    WHERE f.user_id = ?
    ORDER BY r.race_date ASC
');
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>

<div class="page-hero">
    <h1>My Favorite Races</h1>
    <p class="subtitle"><?= count($favorites) ?> races saved</p>
</div>

<?php if (empty($favorites)): ?>
    <div class="no-results">
        <p>You haven't added any favorite races yet.</p>
        <a href="/races.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Races</a>
    </div>
<?php else: ?>
<div class="races-grid">
    <?php foreach ($favorites as $i => $race): ?>
    <div class="race-card">
        <div class="race-card-header">
            <div>
                <div class="race-round">Round <?= $i + 1 ?></div>
                <div style="color:var(--text-muted);font-size:0.8rem;margin-top:2px;">
                    <?= date('M d, Y', strtotime($race['race_date'])) ?>
                </div>
            </div>
            <div class="race-flag"><?= $race['flag_emoji'] ?></div>
        </div>
        <div class="race-card-body">
            <div class="race-gp-name"><?= htmlspecialchars($race['grand_prix_name']) ?></div>
            <div class="race-circuit">📍 <?= htmlspecialchars($race['circuit_name']) ?></div>
            <div class="race-circuit">🌍 <?= htmlspecialchars($race['country']) ?></div>
        </div>
        <div class="race-card-footer">
            <span class="badge badge-<?= $race['status'] ?>"><?= ucfirst($race['status']) ?></span>
            <div class="d-flex gap-1 align-center">
                <button
                    class="fav-btn active"
                    data-race-id="<?= $race['id'] ?>"
                    onclick="toggleFavorite(this, <?= $race['id'] ?>)"
                    title="Remove from favorites"
                >❤️ Fav</button>
                <a href="/pages/race.php?id=<?= $race['id'] ?>" class="btn btn-primary btn-sm">View →</a>
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
