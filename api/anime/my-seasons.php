<?php

require('auth.php');

require("../../db.php");

header("Content-type: application/json");


if(isset($_GET['animeid']) && $_GET['animeid'] != "" && isset($_GET['season'])) {
    
    $anime_id = $_GET['animeid'];
    $season = $_GET['season'];
    
    $res = $pdo->query("SELECT * FROM my_seasons WHERE anime_id = $anime_id && my_season_num = $season")->fetchAll();

}

else if(isset($_GET['seasonid'])) {
    
    $seasonId = $_GET['seasonid'];
    
    $res = $pdo->query("SELECT * FROM my_seasons WHERE my_season_id = $seasonId")->fetchAll();

}

else if(isset($_GET['animeid']) && $_GET['animeid'] != "") {
    
    $anime_id = $_GET['animeid'];
    
    $res = $pdo->query("SELECT DISTINCT s.* FROM my_seasons s JOIN Episodes e ON e.my_season_id = s.my_season_id JOIN EpisodeLinks el ON el.episode_id = e.episode_id WHERE anime_id = $anime_id")->fetchAll();
    
    
} else if(isset($_GET['slug'])) {
    
    $slug = $_GET['slug'];
    
    $res = $pdo->query("SELECT my_seasons.* FROM my_seasons JOIN Animes ON Animes.anime_id = my_seasons.anime_id WHERE Animes.slug = '$slug'")->fetchAll();
    
}

echo json_encode($res);