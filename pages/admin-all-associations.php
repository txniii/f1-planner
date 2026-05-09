<?php
$pageTitle = 'Unassigned Races — F1 Planner';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();
$db = getDatabaseConnection();

$name      = trim($_GET['name'] ?? '');
$year      = trim($_GET['year'] ?? '');
$sort      = $_GET['sort'] ?? 'race_date';
$direction = $_GET['direction'] ?? 'asc';
$limit     = (int)($_GET['limit'] ?? 25);

if ($limit < 1 || $limit > 100) {
    $limit = 25;
}

$validSorts = ['grand_prix_name', 'country', 'race_date'];
if (!in_array($sort, $validSorts, true)) {
    $sort = 'race_date';
}
$direction = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';

$where = ['ur.id IS NULL'];
$params = [];

if ($name !== '') {
    $where[] = 'r.grand_prix_name LIKE ?';
    $params[] = '%' . $name . '%';
}

if ($year !== '') {
    $where[] = 'YEAR(r.race_date) = ?';
    $params[] = (int)$year;
}

$countSql = '
    SELECT COUNT(*) AS total_unassigned
      FROM races r
 LEFT JOIN user_races ur ON ur.race_id = r.id
';
if ($where) {
    $countSql .= ' WHERE ' . implode(' AND ', $where);
}
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalUnassigned = (int)($stmt->fetch()['total_unassigned'] ?? 0);

$listSql = '
    SELECT r.id, r.grand_prix_name, r.country, r.race_date, r.status
      FROM races r
 LEFT JOIN user_races ur ON ur.race_id = r.id
';
if ($where) {
    $listSql .= ' WHERE ' . implode(' AND ', $where);
}
$listSql .= " ORDER BY {$sort} {$direction} LIMIT ?";
$paramsWithLimit = $params;
$paramsWithLimit[] = $limit;

$stmt = $db->prepare($listSql);
$stmt->execute($paramsWithLimit);
$races = $stmt->fetchAll();
$totalShown = count($races);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>Unassigned Races</h1>
    <p class="subtitle">Races that are not in any user planner.</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value"><?= $totalUnassigned ?></div>
        <div class="stat-label">Total Unassigned</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $totalShown ?></div>
        <div class="stat-label">Total Shown</div>
    </div>
</div>

<form method="GET" class="filters-bar">
    <input
        class="filter-input"
        type="text"
        name="name"
        placeholder="Race name..."
        value="<?= htmlspecialchars($name) ?>"
    >
    <input
        class="filter-input"
        type="number"
        name="year"
        placeholder="Year (optional)"
        value="<?= htmlspecialchars($year) ?>"
    >
    <select class="filter-select" name="sort">
        <option value="race_date" <?= $sort === 'race_date' ? 'selected' : '' ?>>Race Date</option>
        <option value="grand_prix_name" <?= $sort === 'grand_prix_name' ? 'selected' : '' ?>>Race Name</option>
        <option value="country" <?= $sort === 'country' ? 'selected' : '' ?>>Country</option>
    </select>
    <select class="filter-select" name="direction">
        <option value="asc" <?= strtolower($direction) === 'asc' ? 'selected' : '' ?>>Ascending</option>
        <option value="desc" <?= strtolower($direction) === 'desc' ? 'selected' : '' ?>>Descending</option>
    </select>
    <input
        class="filter-input"
        type="number"
        name="limit"
        min="1"
        max="100"
        value="<?= htmlspecialchars((string)$limit) ?>"
        placeholder="Limit"
    >
    <button type="submit" class="btn btn-primary">Apply Filters</button>
</form>

<?php if (!$races): ?>
    <div class="no-results">No results available.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Race</th>
            <th>Country</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($races as $race): ?>
            <tr>
                <td><?= htmlspecialchars($race['grand_prix_name']) ?></td>
                <td><?= htmlspecialchars($race['country']) ?></td>
                <td><?= htmlspecialchars($race['race_date']) ?></td>
                <td><?= htmlspecialchars(ucfirst($race['status'])) ?></td>
                <td>
                    <a href="/pages/race.php?id=<?= (int)$race['id'] ?>" class="btn btn-sm btn-secondary">Details</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
