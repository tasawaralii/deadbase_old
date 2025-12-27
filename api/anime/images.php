<?php

require("../../db.php");
require("functions.php");

header("Content-type: application/json");

$mySeasonId = $_GET['season'];
$quality = $_GET['quality'];
$available = (isset($_GET['available']) && $_GET['available'] == "true" ? "true" : "false");
$limit = (isset($_GET['limit']) ? " LIMIT " . $_GET['limit'] : "");
$order = (isset($_GET['random']) && $_GET['random'] == "true" ? "ORDER BY RAND()" : "");

if($available) {
    
    $res = $pdo->query("SELECT Episodes.img FROM Episodes
                        WHERE Episodes.my_season_id = $mySeasonId AND Episodes.img != '' AND EXISTS (SELECT 1 FROM EpisodeLinks WHERE EpisodeLinks.episode_id = Episodes.episode_id) $order $limit")->fetchAll();
    
} else  {
    
    $res = $pdo->query("SELECT Episodes.img FROM Episodes  WHERE Episodes.my_season_id = $mySeasonId AND Episodes.img != ''")->fetchAll();
    
}


            
foreach($res as &$s) {
    
    $s['img'] = makeImgUrl("tmdb",$s['img'],$quality);
}
            
echo json_encode($res);
