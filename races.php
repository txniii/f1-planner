<?php
$pageTitle = 'F1 2025 Race Calendar';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$db = getDatabaseConnection();

$search  = trim($_GET['search'] ?? '');
$status  = $_GET['status'] ?? '';
$country = $_GET['country'] ?? '';

$sql    = 'SELECT * FROM races WHERE 1=1';
$params = [];

if ($search !== '') {
    $sql .= ' AND (grand_prix_name LIKE ? OR country LIKE ? OR circuit_name LIKE ?)';
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
}
if ($status !== '') {
    $sql .= ' AND status = ?';
    $params[] = $status;
}
if ($country !== '') {
    $sql .= ' AND country = ?';
    $params[] = $country;
}
$sql .= ' ORDER BY race_date ASC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$races = $stmt->fetchAll();

$countries = $db->query('SELECT DISTINCT country FROM races ORDER BY country')->fetchAll(PDO::FETCH_COLUMN);

$favoriteIds = [];
if (isLoggedIn()) {
    $fstmt = $db->prepare('SELECT race_id FROM favorites WHERE user_id = ?');
    $fstmt->execute([$_SESSION['user_id']]);
    $favoriteIds = $fstmt->fetchAll(PDO::FETCH_COLUMN);
}

$totalRaces     = $db->query('SELECT COUNT(*) FROM races')->fetchColumn();
$completedRaces = $db->query("SELECT COUNT(*) FROM races WHERE status='completed'")->fetchColumn();
$upcomingRaces  = $totalRaces - $completedRaces;
?>

<div class="page-hero">
    <h1>2025 <span class="red-accent">Formula 1</span> Season</h1>
    <p class="subtitle"><?= $totalRaces ?> races • <?= $completedRaces ?> completed • <?= $upcomingRaces ?> upcoming</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value"><?= $totalRaces ?></div>
        <div class="stat-label">Total Races</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $completedRaces ?></div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $upcomingRaces ?></div>
        <div class="stat-label">Upcoming</div>
    </div>
    <?php if (isLoggedIn()): ?>
    <div class="stat-card">
        <div class="stat-value"><?= count($favoriteIds) ?></div>
        <div class="stat-label">My Favorites</div>
    </div>
    <?php endif; ?>
</div>

<form method="GET" class="filters-bar">
    <input
        class="filter-input"
        type="text"
        name="search"
        placeholder="Search race, country or circuit..."
        value="<?= htmlspecialchars($search) ?>"
    >
    <select class="filter-select" name="status" onchange="this.form.submit()">
        <option value="">All Races</option>
        <option value="upcoming" <?= $status === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>
    <select class="filter-select" name="country" onchange="this.form.submit()">
        <option value="">All Countries</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $country === $c ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search || $status || $country): ?>
        <a href="/races.php" class="btn btn-outline">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($races)): ?>
    <div class="no-results">No races found matching your search.</div>
<?php else: ?>
<div class="races-grid" id="races-grid">
    <?php foreach ($races as $i => $race): ?>
    <?php $isFav = in_array($race['id'], $favoriteIds); ?>
    <div class="race-card" data-race-id="<?= $race['id'] ?>">
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
                <?php if (isLoggedIn()): ?>
                <button
                    class="fav-btn <?= $isFav ? 'active' : '' ?>"
                    data-race-id="<?= $race['id'] ?>"
                    onclick="toggleFavorite(this, <?= $race['id'] ?>)"
                    title="<?= $isFav ? 'Remove from favorites' : 'Add to favorites' ?>"
                >❤️ Fav</button>
                <?php endif; ?>
                <a href="/pages/race.php?id=<?= $race['id'] ?>" class="btn btn-primary btn-sm">View →</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
