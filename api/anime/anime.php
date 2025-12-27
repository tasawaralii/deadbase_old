<?php

require('auth.php');

require("../../db.php");

header("Content-type: application/json");


$from = (isset($_GET['short']) && $_GET['short'] == "true" ? "Animes" : "deadstream"); 


if(isset($_GET['animeid'])) {
    
    $anime_id = $_GET['animeid'];
    
    $res = $pdo->query("SELECT * FROM $from WHERE anime_id = $anime_id")->fetch();
    
    
} else if(isset($_GET['dummyid'])) {
    
    $anime_id = $_GET['dummyid'];
    
    $res = $pdo->query("SELECT * FROM $from WHERE anime_id = $anime_id")->fetch();
    
    
} else if(isset($_GET['slug'])) {
    
    $slug = $_GET['slug'];
    
    $res = $pdo->query("SELECT * FROM $from WHERE slug = '$slug'")->fetch();
    
} else if(isset($_GET['seasonid'])) {
    
    $mySeasonId = $_GET['seasonid'];
    
    $res = $pdo->query("SELECT $from.* FROM $from JOIN my_seasons ON my_seasons.anime_id = $from.anime_id WHERE my_seasons.my_season_id = $mySeasonId")->fetch();
    
}


$anime_id = $res['anime_id'];


$pdo->query("INSERT INTO AnimeViews (anime_id, view_date, daily_views) VALUES ($anime_id , CURRENT_DATE, 1) ON DUPLICATE KEY UPDATE daily_views = daily_views + 1");

// makeImgUrlAnime($anime[0],"high");
            
echo json_encode($res);