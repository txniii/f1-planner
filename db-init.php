<?php
/**
 * One-time DB initializer for f1-planner
 * Uses same env vars as app, idempotent.
 * Run: php public/db-init.php
 * Delete after success.
 */

require_once __DIR__ . '/../includes/db.php';

try {
    $db = getDatabaseConnection();

    // Run schema (IF NOT EXISTS makes idempotent)
$schema = file_get_contents(__DIR__ . '/database.sql');
$schema = str_replace([
    "CREATE DATABASE IF NOT EXISTS f1_planner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\\n",
    "USE mb963;\\n"
], '', $schema);
    $db->exec($schema);

    echo "✅ Success! Database initialized.\n";
    echo "Tables created: races (24 records), users, sessions, favorites, notes.\n";
    echo "Test: SELECT COUNT(*) FROM races; → 24\n";
    echo "Delete this file when done.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Check env vars DB_HOST, DB_USER, DB_PASS, DB_NAME.\n";
}
?>

