<?php
$pageTitle = 'Register — F1 Planner';
require_once __DIR__ . '/includes/auth.php';

startSession();
if (isLoggedIn()) { header('Location: /index.php'); exit; }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $result = registerUser($username, $email, $password);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            // Auto login after register
            loginUser($email, $password);
            header('Location: /index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700;900&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-f1">F1</span>
            <span class="logo-text"> Planner</span>
        </div>
        <div class="auth-title">Create Account</div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input class="form-input" type="text" name="username" required
                    placeholder="f1fan99"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" type="email" name="email" required
                    placeholder="your@email.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" required placeholder="Min. 6 characters">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input class="form-input" type="password" name="confirm" required placeholder="Repeat password">
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="/login.php">Login</a>
        </div>
    </div>
</div>
</body>
</html>
