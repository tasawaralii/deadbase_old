<?php
// Your database connection code here
require_once('db.php');
// Fetch all Animes
$query = "SELECT * FROM Animes";
$result = mysqli_query($conn, $query);

echo "<h1>Animes</h1>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<a href='show_seasons.php?anime_id={$row['anime_id']}'>{$row['anime_name']}</a><br>";
}

mysqli_close($conn);
?>