<?php
$pageTitle = 'Dashboard — F1 Planner';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$db = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_favorite'], $_POST['race_id'])) {
    try {
        $stmt = $db->prepare("INSERT IGNORE INTO favorites (user_id, race_id) VALUES (?, ?)");
        $stmt->execute([$user_id, (int) $_POST['race_id']]);
        header("Location: /dashboard.php");
        exit;
    } catch (Exception $e) {
        $favoriteError = $e->getMessage();
    }
}

try {
    $stmt = $db->prepare("
        SELECT r.*, COUNT(f.id) as is_favorite
        FROM races r
        LEFT JOIN favorites f ON r.id = f.race_id AND f.user_id = ?
        GROUP BY r.id
        ORDER BY r.race_date ASC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $upcomingRaces = $stmt->fetchAll();

    $stmt = $db->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $totalFavorites = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $totalNotes = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM races WHERE status = 'completed'");
    $stmt->execute();
    $racesWatched = $stmt->fetchColumn();
} catch (Exception $e) {
    $upcomingRaces = [];
    $totalFavorites = 0;
    $totalNotes = 0;
    $racesWatched = 0;
}
?>

<div class="page-hero">
    <h1>Dashboard</h1>
    <p class="subtitle">Welcome back!</p>
</div>

<?php if (!empty($favoriteError)): ?>
    <div class="no-results"><?= htmlspecialchars($favoriteError) ?></div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value"><?= $totalFavorites ?></div>
        <div class="stat-label">Favorite Races</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalNotes ?></div>
        <div class="stat-label">Your Notes</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $racesWatched ?></div>
        <div class="stat-label">Races Completed</div>
    </div>
</div>

<section>
    <h2>Upcoming Races</h2>

    <?php if (empty($upcomingRaces)): ?>
        <div class="no-results">No races found.</div>
    <?php else: ?>
        <div class="races-grid">
            <?php foreach ($upcomingRaces as $race): ?>
                <div class="race-card">
                    <div class="race-card-header">
                        <div>
                            <div class="race-round"><?= htmlspecialchars($race['grand_prix_name']) ?></div>
                            <div style="color:var(--text-muted);font-size:0.8rem;margin-top:2px;">
                                <?= date('M d, Y', strtotime($race['race_date'])) ?>
                            </div>
                        </div>
                        <div class="race-flag"><?= htmlspecialchars($race['flag_emoji']) ?></div>
                    </div>

                    <div class="race-card-body">
                        <div class="race-circuit">📍 <?= htmlspecialchars($race['circuit_name']) ?></div>
                        <div class="race-circuit">🌍 <?= htmlspecialchars($race['country']) ?></div>
                    </div>

                    <div class="race-card-footer">
                        <span class="badge badge-<?= htmlspecialchars($race['status']) ?>">
                            <?= ucfirst($race['status']) ?>
                        </span>

                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="race_id" value="<?= (int)$race['id'] ?>">
                            <button type="submit" name="add_favorite" class="btn btn-primary btn-sm">
                                <?= $race['is_favorite'] ? 'Added' : 'Add to Favorites' ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div style="margin-top: 3rem; text-align: center;">
    <a href="/races.php" class="btn btn-primary">Browse All Races</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
