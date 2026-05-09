<?php
// pages/admin-sessions.php
$pageTitle = 'Manage Sessions — Admin';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$db = getDatabaseConnection();

// Fetch all races for the dropdown
$racesStmt = $db->query('SELECT id, grand_prix_name, country FROM races ORDER BY race_date');
$races = $racesStmt->fetchAll();

// Determine selected race
$selectedRaceId = isset($_GET['race_id']) ? (int)$_GET['race_id'] : null;
if (!$selectedRaceId && !empty($races)) {
    $selectedRaceId = (int)$races[0]['id'];
}

// Handle delete session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_session_id'])) {
    $deleteId = (int)$_POST['delete_session_id'];
    $stmt = $db->prepare('DELETE FROM sessions WHERE id = ?');
    $stmt->execute([$deleteId]);

    $raceId = (int)($_POST['race_id'] ?? $selectedRaceId);
    header('Location: /pages/admin-sessions.php?race_id=' . $raceId);
    exit;
}

// Handle update session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_session_id'])) {
    $id              = (int)$_POST['update_session_id'];
    $sessionType     = trim($_POST['session_type'] ?? '');
    $sessionStartUtc = $_POST['session_start_utc'] ?? '';
    $raceId          = (int)($_POST['race_id'] ?? 0);

    if ($id && $raceId && $sessionType && $sessionStartUtc) {
        $stmt = $db->prepare(
            'UPDATE sessions
             SET session_type = ?, session_start_utc = ?
             WHERE id = ? AND race_id = ?'
        );
        $stmt->execute([$sessionType, $sessionStartUtc, $id, $raceId]);
    }

    header('Location: /pages/admin-sessions.php?race_id=' . $raceId);
    exit;
}

// Handle create session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_session'])) {
    $raceId          = (int)($_POST['race_id'] ?? 0);
    $sessionType     = trim($_POST['session_type'] ?? '');
    $sessionStartUtc = $_POST['session_start_utc'] ?? '';

    if ($raceId && $sessionType && $sessionStartUtc) {
        $stmt = $db->prepare(
            'INSERT INTO sessions (race_id, session_type, session_start_utc)
             VALUES (?, ?, ?)'
        );
        $stmt->execute([$raceId, $sessionType, $sessionStartUtc]);
    }

    header('Location: /pages/admin-sessions.php?race_id=' . $raceId);
    exit;
}

// Fetch sessions for selected race
$sessions = [];
$selectedRace = null;
if ($selectedRaceId) {
    $raceStmt = $db->prepare('SELECT * FROM races WHERE id = ?');
    $raceStmt->execute([$selectedRaceId]);
    $selectedRace = $raceStmt->fetch();

    $sessionsStmt = $db->prepare(
        'SELECT * FROM sessions
         WHERE race_id = ?
         ORDER BY session_start_utc'
    );
    $sessionsStmt->execute([$selectedRaceId]);
    $sessions = $sessionsStmt->fetchAll();
}

include __DIR__ . '/../includes/header.php';
?>

<h1>Manage Sessions</h1>

<p>As an admin you can manage FP1/FP2/Sprint/Quali/Race times for each Grand Prix.</p>

<form method="GET" class="form-inline" style="margin-bottom: 1.5rem;">
    <label class="form-label" for="race_id">Select race:</label>
    <select id="race_id" name="race_id" class="form-input" onchange="this.form.submit()">
        <?php foreach ($races as $race): ?>
            <option
                value="<?= htmlspecialchars($race['id']) ?>"
                <?= $selectedRaceId === (int)$race['id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($race['grand_prix_name'] . ' (' . $race['country'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit" class="btn btn-sm">Go</button></noscript>
</form>

<?php if ($selectedRace): ?>
    <h2><?= htmlspecialchars($selectedRace['grand_prix_name']) ?> sessions</h2>

    <section class="admin-section">
        <h3>Create new session</h3>
        <form method="POST" class="form-grid">
            <input type="hidden" name="create_session" value="1" />
            <input type="hidden" name="race_id" value="<?= htmlspecialchars($selectedRaceId) ?>" />

            <div class="form-group">
                <label class="form-label">Session type</label>
                <select name="session_type" class="form-input" required>
                    <option value="">Select</option>
                    <option value="FP1">FP1</option>
                    <option value="FP2">FP2</option>
                    <option value="FP3">FP3</option>
                    <option value="Sprint">Sprint</option>
                    <option value="Qualifying">Qualifying</option>
                    <option value="Race">Race</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Session start (UTC)</label>
                <input
                    type="datetime-local"
                    name="session_start_utc"
                    class="form-input"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">Create session</button>
        </form>
    </section>

    <section class="admin-section" style="margin-top: 2rem;">
        <h3>Edit existing sessions</h3>

        <?php if (empty($sessions)): ?>
            <p>No sessions defined for this race yet.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Start (UTC)</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sessions as $session): ?>
                    <tr>
                        <form method="POST">
                            <td><?= htmlspecialchars($session['id']) ?></td>
                            <td>
                                <select name="session_type" class="form-input">
                                    <?php
                                    $types = ['FP1', 'FP2', 'FP3', 'Sprint', 'Qualifying', 'Race'];
                                    foreach ($types as $t):
                                    ?>
                                        <option
                                            value="<?= htmlspecialchars($t) ?>"
                                            <?= $session['session_type'] === $t ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($t) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input
                                    type="datetime-local"
                                    name="session_start_utc"
                                    class="form-input"
                                    value="<?= htmlspecialchars(str_replace(' ', 'T', substr($session['session_start_utc'], 0, 16))) ?>"
                                >
                            </td>
                            <td>
                                <input type="hidden" name="update_session_id"
                                       value="<?= htmlspecialchars($session['id']) ?>" />
                                <input type="hidden" name="race_id"
                                       value="<?= htmlspecialchars($selectedRaceId) ?>" />
                                <button type="submit" class="btn btn-sm">Save</button>
                        </form>
                        <form method="POST" style="display:inline"
                              onsubmit="return confirm('Delete this session?');">
                            <input type="hidden" name="delete_session_id"
                                   value="<?= htmlspecialchars($session['id']) ?>" />
                            <input type="hidden" name="race_id"
                                   value="<?= htmlspecialchars($selectedRaceId) ?>" />
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                            </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
<?php else: ?>
    <p>No races found.</p>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
