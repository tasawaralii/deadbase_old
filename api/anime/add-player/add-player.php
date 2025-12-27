<?php

require("../../../db.php");
header("content-type: application/json");

if(isset($_GET['epid'])) {
    
    $link = [];
    
    $epid = $_GET['epid'];
    $anime_name = preg_replace("/[^a-zA-Z0-9\- ]/", "", $_GET['aname']);
    $season = $_GET['season'];
    $episode = $_GET['episode'];
    
    echo $name = $anime_name . " - " . "S" . $season . "E" . $episode;
    
    $link = $pdo->query("SELECT * FROM EpisodeLinks WHERE episode_id = $epid ORDER BY EpisodeLinks.quality_order DESC LIMIT 1")->fetch();
    $drive_id = $link['drive_id'];
    
    $pdo->query("INSERT INTO deadstream_playerx (is_episode,cor_id,drive_id,name) VALUES (1,$epid,'$drive_id','$name') ON DUPLICATE KEY UPDATE priority = priority + 1");
    
    print_r(json_encode($link));
} else if(isset($_GET['dumyid'])) {
    
    $dumyid = $_GET['dumyid'];
    $anime_name = preg_replace("/[^a-zA-Z0-9\- ]/", "", $_GET['aname']);
    
    echo $name = $anime_name . " - Movie" ;
    
    $res = $pdo->query("SELECT movieLinks.* FROM movieLinks JOIN Animes ON Animes.anime_id = movieLinks.anime_id WHERE Animes.dumy_id = $dumyid ORDER BY movieLinks.size DESC LIMIT 1")->fetch();
    $drive_id = $res['movie_drive_id'];
    
    $pdo->query("INSERT INTO deadstream_playerx (is_episode,cor_id,drive_id,name) VALUES (0,$dumyid,'$drive_id','$name') ON DUPLICATE KEY UPDATE priority = priority + 1");

}