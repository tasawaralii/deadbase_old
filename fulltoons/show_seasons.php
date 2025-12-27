<?php
// Your database connection code here
require_once('db.php');
$anime_id = $_GET['anime_id'];

// Fetch Seasons for the selected Anime
$query = "SELECT * FROM Seasons WHERE anime_id = $anime_id";
$result = mysqli_query($conn, $query);

echo "<h1>Seasons</h1>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<a href='show_episodes.php?season_id={$row['tmdb_id']}'>{$row['season_name']}</a><br>";
}

mysqli_close($conn);
?>