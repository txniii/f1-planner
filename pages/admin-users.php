<?php
// pages/admin-users.php
$pageTitle = 'Manage Users — Admin';

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$db = getDatabaseConnection();

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $userId = (int)$_POST['user_id'];
    $role   = $_POST['role'] === 'admin' ? 'admin' : 'user';

    // Optional: prevent demoting yourself
    startSession();
    if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $userId) {
        // Do nothing or show message if you want
    } else {
        $stmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$role, $userId]);
    }

    header('Location: /pages/admin-users.php');
    exit;
}

// Fetch all users
$stmt = $db->query('SELECT id, username, email, role, created_at FROM users ORDER BY id');
$users = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<h1>Manage Users</h1>

<p>Change user roles between standard user and admin.</p>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['id']) ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td>
                <form method="POST" class="form-inline">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($u['id']) ?>" />
                    <select name="role" class="form-input">
                        <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>user</option>
                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                    </select>
                    <button type="submit" class="btn btn-sm">Save</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../includes/footer.php'; ?>
