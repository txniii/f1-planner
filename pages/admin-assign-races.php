<?php
$pageTitle = 'Assign Races To Users — F1 Planner';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();
$db = getDatabaseConnection();

$raceTerm = trim($_GET['race_term'] ?? '');
$userTerm = trim($_GET['user_term'] ?? '');

$races = [];
$users = [];

if ($raceTerm !== '') {
    $stmt = $db->prepare('
        SELECT id, grand_prix_name, country, race_date
        FROM races
        WHERE grand_prix_name LIKE ?
        ORDER BY race_date ASC
        LIMIT 25
    ');
    $stmt->execute(['%' . $raceTerm . '%']);
    $races = $stmt->fetchAll();
}

if ($userTerm !== '') {
    $stmt = $db->prepare('
        SELECT id, username, email, role
        FROM users
        WHERE username LIKE ? OR email LIKE ?
        ORDER BY username ASC
        LIMIT 25
    ');
    $like = '%' . $userTerm . '%';
    $stmt->execute([$like, $like]);
    $users = $stmt->fetchAll();
}

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRaces = $_POST['race_ids'] ?? [];
    $selectedUsers = $_POST['user_ids'] ?? [];

    if (!is_array($selectedRaces) || !is_array($selectedUsers) ||
        count($selectedRaces) === 0 || count($selectedUsers) === 0) {
        $feedback = 'Please select at least one race and one user.';
    } else {
        $created = 0;

        foreach ($selectedUsers as $userId) {
            foreach ($selectedRaces as $raceId) {
                $stmt = $db->prepare('
                    INSERT IGNORE INTO user_races (user_id, race_id)
                    VALUES (?, ?)
                ');
                $stmt->execute([(int)$userId, (int)$raceId]);
                if ($stmt->rowCount() > 0) {
                    $created++;
                }
            }
        }

        $feedback = "Applied {$created} new association(s).";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>Assign Races To Users</h1>
    <p class="subtitle">Admin tool to map races to user planners.</p>
</div>

<?php if ($feedback): ?>
    <div class="alert alert-success"><?= htmlspecialchars($feedback) ?></div>
<?php endif; ?>

<form method="GET" class="filters-bar">
    <input
        class="filter-input"
        type="text"
        name="race_term"
        placeholder="Race name (partial)"
        value="<?= htmlspecialchars($raceTerm) ?>"
    >
    <input
        class="filter-input"
        type="text"
        name="user_term"
        placeholder="Username or email (partial)"
        value="<?= htmlspecialchars($userTerm) ?>"
    >
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<?php if (empty($races) && empty($users) && ($raceTerm || $userTerm)): ?>
    <div class="no-results">No races or users found for the given filters.</div>
<?php endif; ?>

<?php if ($races || $users): ?>
<form method="POST" class="mt-3">
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-value"><?= count($races) ?></div>
            <div class="stat-label">Matched Races (max 25)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= count($users) ?></div>
            <div class="stat-label">Matched Users (max 25)</div>
        </div>
    </div>

    <div class="mt-2">
        <h2>Races</h2>
        <?php if (!$races): ?>
            <p class="text-muted">No races matched.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($races as $race): ?>
                    <li>
                        <label>
                            <input type="checkbox" name="race_ids[]" value="<?= (int)$race['id'] ?>">
                            <?= htmlspecialchars($race['grand_prix_name']) ?>
                            (<?= htmlspecialchars($race['country']) ?>,
                            <?= date('Y-m-d', strtotime($race['race_date'])) ?>)
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="mt-3">
        <h2>Users</h2>
        <?php if (!$users): ?>
            <p class="text-muted">No users matched.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($users as $u): ?>
                    <li>
                        <label>
                            <input type="checkbox" name="user_ids[]" value="<?= (int)$u['id'] ?>">
                            <?= htmlspecialchars($u['username']) ?> – <?= htmlspecialchars($u['email']) ?>
                            (<?= htmlspecialchars($u['role']) ?>)
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Apply Checked Associations</button>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
