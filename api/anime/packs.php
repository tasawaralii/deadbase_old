<?php

require("../../db.php");
require("config.php");
require("functions.php");

function structPacks($pdo,$mySeasonId,$deaddrive,$onlyHindi = 0) {
    
    $res = $pdo->query("SELECT (CONCAT(Packs.startEp , ' - ', Packs.endEp)) AS hehe, Packs.*,Links_info.uid FROM Packs JOIN Links_info on Links_info.Id = Packs.drive_id WHERE my_season_id = $mySeasonId AND Packs.honly = $onlyHindi ORDER BY Packs.startEp ASC,Packs.endEp DESC,Packs.size ASC")->fetchAll(PDO::FETCH_GROUP);
    
    foreach($res as &$range) {
        foreach($range as &$quality) {
            $quality['uid'] = $deaddrive['download'] . $quality['uid'];
            $quality['encoded'] = AES("encrypt",$quality['uid']);
            $quality['redirect'] = "/redirect?url=" . $quality['encoded'];
        }
    }
    return $res;
}

header("Content-type: application/json");

$res = [];

if(isset($_GET['anime'])) {
    
    $anime_id = $_GET['anime'];
    $onlyHindi = (isset($_GET['onlyhindi']) && $_GET['onlyhindi'] == "1" ? "1" : "0");
    
    $allSeasons = $pdo->query("SELECT my_seasons.* FROM my_seasons WHERE my_seasons.anime_id = $anime_id AND EXISTS (SELECT 1 FROM Packs WHERE Packs.my_season_id = my_seasons.my_season_id)")->fetchAll();
    
    foreach($allSeasons as $season) {
        
        $tem = structPacks($pdo,$season['my_season_id'],$deaddrive,$onlyHindi);
        
        if($tem)
            $res[$season['my_season_num']] = $tem;
        
    }
    
}
else {

$mySeasonId = $_GET['season'];

$res = structPacks($pdo,$mySeasonId,$deaddrive);
    
}
echo json_encode($res);
