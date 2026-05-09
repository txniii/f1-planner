<?php
$pageTitle = 'Manage Races — Admin';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$db = getDatabaseConnection();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $stmt = $db->prepare('DELETE FROM races WHERE id = ?');
    $stmt->execute([$deleteId]);
    header('Location: /pages/admin-races.php');
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = (int)$_POST['update_id'];
    $grandPrixName = trim($_POST['grand_prix_name'] ?? '');
    $country       = trim($_POST['country'] ?? '');
    $circuitName   = trim($_POST['circuit_name'] ?? '');
    $raceDate      = $_POST['race_date'] ?? '';
    $description   = trim($_POST['description'] ?? '');
    $flagEmoji     = trim($_POST['flag_emoji'] ?? '');
    $circuitLength = trim($_POST['circuit_length'] ?? '');
    $lapCount      = (int)($_POST['lap_count'] ?? 0);
    $status        = $_POST['status'] ?? 'upcoming';

    if ($grandPrixName && $country && $circuitName && $raceDate) {
        $stmt = $db->prepare(
            'UPDATE races
             SET grand_prix_name = ?, country = ?, circuit_name = ?, race_date = ?,
                 description = ?, flag_emoji = ?, circuit_length = ?, lap_count = ?, status = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $grandPrixName,
            $country,
            $circuitName,
            $raceDate,
            $description,
            $flagEmoji,
            $circuitLength,
            $lapCount,
            $status,
            $id
        ]);
        header('Location: /pages/admin-races.php');
        exit;
    }
}

// Fetch races
$stmt = $db->query('SELECT * FROM races ORDER BY race_date');
$races = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Manage Races</h1>

<p>You can edit race names, dates, circuits, lap counts, and status here.</p>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Grand Prix</th>
        <th>Country</th>
        <th>Circuit</th>
        <th>Date</th>
        <th>Laps</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($races as $race): ?>
        <tr>
            <form method="POST">
                <td><?= htmlspecialchars($race['id']) ?></td>
                <td>
                    <input type="text" name="grand_prix_name"
                           value="<?= htmlspecialchars($race['grand_prix_name']) ?>" />
                </td>
                <td>
                    <input type="text" name="country"
                           value="<?= htmlspecialchars($race['country']) ?>" />
                </td>
                <td>
                    <input type="text" name="circuit_name"
                           value="<?= htmlspecialchars($race['circuit_name']) ?>" />
                </td>
                <td>
                    <input type="date" name="race_date"
                           value="<?= htmlspecialchars($race['race_date']) ?>" />
                </td>
                <td>
                    <input type="number" name="lap_count" min="1"
                           value="<?= htmlspecialchars($race['lap_count']) ?>" />
                </td>
                <td>
                    <select name="status">
                        <option value="upcoming" <?= $race['status'] === 'upcoming' ? 'selected' : '' ?>>upcoming</option>
                        <option value="completed" <?= $race['status'] === 'completed' ? 'selected' : '' ?>>completed</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="update_id" value="<?= htmlspecialchars($race['id']) ?>" />
                    <button type="submit" class="btn btn-sm">Save</button>
            </form>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this race?');">
                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($race['id']) ?>" />
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
                </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
