<?php

require("../../db.php");

header("Content-type: application/json");

$res = $pdo->query("SELECT Animes.anime_name, SUM(AnimeViews.daily_views) AS views
            FROM Animes
            JOIN AnimeViews ON Animes.anime_id = AnimeViews.anime_id
            GROUP BY Animes.anime_id
            ORDER BY views DESC LIMIT 10")->fetchAll();
            


$json = json_encode($res);

if ($json === false) {
    echo json_last_error_msg(); // Display the JSON encoding error
} else {
    echo $json; // Output JSON if encoding is successful
}