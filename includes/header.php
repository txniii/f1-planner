<?php
require_once __DIR__ . '/auth.php';

startSession();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'F1 Planner') ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700;900&family=Orbitron:wght@700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="container">
        <div class="nav-brand">
            <a href="/index.php">🏎 F1 Planner</a>
        </div>
        <ul class="nav-links">
            <?php if (isLoggedIn()): ?>
                <li><a href="/dashboard.php">Dashboard</a></li>
                <li><a href="/races.php">Races</a></li>
                <li><a href="/pages/favorites.php">My Favorites</a></li>
                <!-- Watchlist removed. Add back or change target if you want a planner page. -->
                <!-- <li><a href="/pages/watchlist.php">My Planner</a></li> -->

                <?php if ($user && ($user['role'] ?? null) === 'admin'): ?>
                    <li><a href="/pages/admin-races.php">Manage Races</a></li>
                    <li><a href="/pages/admin-assign-races.php">Assign Races</a></li>
                    <li><a href="/pages/admin-unassigned-races.php">Unassigned</a></li>
                    <li><a href="/pages/admin-all-associations.php">All Associations</a></li>
                <?php endif; ?>

                <li class="nav-user">
                    <span>👤 <?= htmlspecialchars($user['username'] ?? 'User') ?></span>
                    <a href="/logout.php" class="btn btn-sm">Logout</a>
                </li>
            <?php else: ?>
                <li><a href="/login.php" class="btn btn-sm">Login</a></li>
                <li><a href="/register.php" class="btn btn-sm btn-primary">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<main class="container">
