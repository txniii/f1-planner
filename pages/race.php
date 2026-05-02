<?php
$pageTitle = 'Race Details — F1 Planner';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();
$race_id = intval($_GET['id'] ?? 0);

if ($race_id <= 0) {
    header('Location: /races.php');
    exit;
}

$userNote = '';
$isFavorited = false;
$noteMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['save_note'])) {
        $note_text = trim($_POST['note_text'] ?? '');

        try {
            $stmt = $db->prepare('SELECT id FROM notes WHERE user_id = ? AND race_id = ?');
            $stmt->execute([$user_id, $race_id]);
            $existingNote = $stmt->fetch();

            if ($existingNote) {
                $stmt = $db->prepare('UPDATE notes SET note_text = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ? AND race_id = ?');
                $stmt->execute([$note_text, $user_id, $race_id]);
            } else {
                $stmt = $db->prepare('INSERT INTO notes (user_id, race_id, note_text) VALUES (?, ?, ?)');
                $stmt->execute([$user_id, $race_id, $note_text]);
            }

            header('Location: /pages/race.php?id=' . $race_id);
            exit;
        } catch (Exception $e) {
            $noteMessage = 'Unable to save note.';
        }
    }

    if (isset($_POST['toggle_favorite'])) {
        try {
            $stmt = $db->prepare('SELECT id FROM favorites WHERE user_id = ? AND race_id = ?');
            $stmt->execute([$user_id, $race_id]);
            $favoriteRow = $stmt->fetch();

            if ($favoriteRow) {
                $stmt = $db->prepare('DELETE FROM favorites WHERE user_id = ? AND race_id = ?');
                $stmt->execute([$user_id, $race_id]);
            } else {
                $stmt = $db->prepare('INSERT INTO favorites (user_id, race_id) VALUES (?, ?)');
                $stmt->execute([$user_id, $race_id]);
            }

            header('Location: /pages/race.php?id=' . $race_id);
            exit;
        } catch (Exception $e) {
            $noteMessage = 'Unable to update favorites.';
        }
    }
}

$stmt = $db->prepare('SELECT * FROM races WHERE id = ?');
$stmt->execute([$race_id]);
$race = $stmt->fetch();

if (!$race) {
    http_response_code(404);
    echo '<div class="container"><h1>Race Not Found</h1><p><a href="/races.php">Back to Races</a></p></div>';
    exit;
}

$stmt = $db->prepare('SELECT * FROM sessions WHERE race_id = ? ORDER BY session_datetime ASC');
$stmt->execute([$race_id]);
$sessions = $stmt->fetchAll();

if (isLoggedIn()) {
    $stmt = $db->prepare('SELECT note_text FROM notes WHERE user_id = ? AND race_id = ?');
    $stmt->execute([$_SESSION['user_id'], $race_id]);
    $noteRow = $stmt->fetch();
    $userNote = $noteRow['note_text'] ?? '';

    $stmt = $db->prepare('SELECT id FROM favorites WHERE user_id = ? AND race_id = ?');
    $stmt->execute([$_SESSION['user_id'], $race_id]);
    $isFavorited = $stmt->fetch() ? true : false;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="race-detail">
    <div class="race-detail-header">
        <div class="race-detail-flag"><?= htmlspecialchars($race['flag_emoji']) ?></div>
        <div>
            <h1><?= htmlspecialchars($race['grand_prix_name']) ?></h1>
            <p class="race-detail-info">
                📍 <?= htmlspecialchars($race['circuit_name']) ?> •
                🌍 <?= htmlspecialchars($race['country']) ?> •
                📅 <?= date('F d, Y', strtotime($race['race_date'])) ?>
            </p>
        </div>
    </div>

    <div class="race-detail-content">
        <section class="race-detail-section">
            <h2>Circuit Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Circuit Length:</strong> <?= htmlspecialchars($race['circuit_length']) ?>
                </div>
                <div class="info-item">
                    <strong>Lap Count:</strong> <?= (int) $race['lap_count'] ?> laps
                </div>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="badge badge-<?= htmlspecialchars($race['status']) ?>">
                        <?= ucfirst($race['status']) ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($race['description'])): ?>
                <div class="race-description">
                    <h3>About This Race</h3>
                    <p><?= htmlspecialchars($race['description']) ?></p>
                </div>
            <?php endif; ?>
        </section>

        <section class="race-detail-section">
            <h2>Session Schedule</h2>
            <?php if (empty($sessions)): ?>
                <p class="text-muted">No sessions scheduled yet.</p>
            <?php else: ?>
                <div class="sessions-list">
                    <?php foreach ($sessions as $session): ?>
                        <div class="session-item">
                            <div class="session-name"><?= htmlspecialchars($session['session_name']) ?></div>
                            <div class="session-datetime">
                                🕐 <?= date('M d, Y \a\t H:i', strtotime($session['session_datetime'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php if (isLoggedIn()): ?>
            <section class="race-detail-section">
                <h2>Your Notes</h2>

                <?php if (!empty($noteMessage)): ?>
                    <p class="text-muted"><?= htmlspecialchars($noteMessage) ?></p>
                <?php endif; ?>

                <form method="POST">
                    <textarea
                        id="note-text"
                        name="note_text"
                        class="form-input"
                        placeholder="Add your personal notes about this race..."
                        rows="5"
                    ><?= htmlspecialchars($userNote) ?></textarea>

                    <div style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button type="submit" name="save_note" class="btn btn-primary">
                            Save Note
                        </button>

                        <button type="submit" name="toggle_favorite" class="btn btn-secondary">
                            <?= $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' ?>
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <div style="margin-top: 1rem;">
            <a href="/races.php" class="btn btn-secondary">← Back to Races</a>
        </div>
    </div>
</div>

<style>
.race-detail-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background: rgba(225, 6, 0, 0.1);
    border: 1px solid rgba(225, 6, 0, 0.3);
    border-radius: 8px;
    margin-bottom: 2rem;
}

.race-detail-flag {
    font-size: 4rem;
}

.race-detail-info {
    color: var(--text-muted);
    font-size: 1rem;
}

.race-detail-content {
    display: grid;
    gap: 2rem;
}

.race-detail-section {
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 2rem;
    border-radius: 8px;
}

.race-detail-section h2 {
    margin-bottom: 1.5rem;
    color: var(--primary);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 4px;
    border-left: 3px solid var(--primary);
}

.race-description {
    margin-top: 1rem;
}

.race-description h3 {
    margin-bottom: 0.5rem;
}

.sessions-list {
    display: grid;
    gap: 1rem;
}

.session-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 4px;
    border-left: 3px solid var(--primary);
}

.session-name {
    font-weight: 600;
    font-size: 1rem;
}

.session-datetime {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.text-muted {
    color: var(--text-muted);
}

@media (max-width: 768px) {
    .race-detail-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .race-detail-flag {
        font-size: 3rem;
    }

    .session-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
