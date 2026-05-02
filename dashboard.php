<?php
// dashboard.php

// Sample data for dashboard stats
$stats = [
    'totalRaces' => 42,
    'completedRaces' => 40,
    'upcomingRaces' => 2,
];

// Sample upcoming races data
$upcomingRaces = [
    [
        'name' => 'Grand Prix of Monaco',
        'date' => '2026-05-22',
    ],
    [
        'name' => 'Canadian Grand Prix',
        'date' => '2026-06-12',
    ],
];

// Display dashboard
echo "<h1>User Dashboard</h1>";

echo "<h2>Statistics:</h2>";
foreach ($stats as $key => $value) {
    echo "<p>" . ucfirst($key) . ": " . $value . "</p>";
}

echo "<h2>Upcoming Races:</h2>";
if (count($upcomingRaces) > 0) {
    echo "<ul>";
    foreach ($upcomingRaces as $race) {
        echo "<li>" . $race['name'] . " - " . $race['date'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No upcoming races.</p>";
}
?>