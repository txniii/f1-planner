<?php
$pageTitle = 'Login — F1 Planner';
require_once __DIR__ . '/includes/auth.php';

startSession();
if (isLoggedIn()) { header('Location: /index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        if (loginUser($email, $password)) {
            header('Location: /index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
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
        <div class="auth-title">Sign In</div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" type="email" name="email" required
                    placeholder="your@email.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="/register.php">Register</a>
        </div>

        <div class="auth-footer" style="margin-top:0.8rem;font-size:0.8rem;color:var(--text-dim)">
            Demo admin: admin@f1planner.com / admin123
        </div>
    </div>
</div>
</body>
</html>
