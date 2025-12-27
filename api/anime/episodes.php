<?php

require('auth.php');

require("../../db.php");

header("Content-type: application/json");

$mySeasonID = $_GET['seasonid'];

$onlyHindi = (isset($_GET['onlyhindi']) && $_GET['onlyhindi'] == "1" ? "AND EpisodeLinks.Hindi_Only = 1" : "");

$res = [];


if(isset($_GET['animeid'])) {

    $anime_id = $_GET['animeid'];

    if(isset($_GET['season'])) {
        
        $season = $_GET['season'];
        
        if($season == "all") {
            
            
            $res = $pdo->query("SELECT my_seasons.my_season_num,Episodes.* FROM Episodes 
                        JOIN my_seasons ON my_seasons.my_season_id = Episodes.my_season_id
                        JOIN Animes ON Animes.anime_id = my_seasons.anime_id
                        WHERE Animes.anime_id = $anime_id AND
                        EXISTS (SELECT 1 FROM EpisodeLinks WHERE Episodes.episode_id = EpisodeLinks.episode_id $onlyHindi)
                        ORDER BY my_seasons.my_season_num ASC,Episodes.epSort ASC")->fetchAll(PDO::FETCH_GROUP);
            
            
        } else {
            
        }
        
    }
}

else if(isset($_GET['seasonid'])) {
        
    $mySeasonId = $_GET['seasonid'];

    $res = $pdo->query("SELECT * FROM Episodes WHERE EXISTS (SELECT 1 FROM EpisodeLinks WHERE episode_id = Episodes.episode_id) AND my_season_id = $mySeasonId ORDER BY Episodes.epSort ASC")->fetchAll();

}


echo json_encode($res);