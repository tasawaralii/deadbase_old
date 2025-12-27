<?php

require('auth.php');


require("../../db.php");
require("config.php");
require("functions.php");
require("telegram.php");

$notify = new Telegram();

header("Content-type: application/json");

$order = (isset($_GET['order']) && $_GET['order'] == "desc" ? "DESC" : "ASC");


if(isset($_GET['new'])) {
    
    $slug = $_GET['slug'];
    
    $sql = "
        SELECT li.uid FROM Links_info li WHERE Id = (
            SELECT movie_drive_id AS Id FROM movieLinks WHERE anime_id = (
            SELECT anime_id FROM Animes WHERE slug = '$slug') AND isStream LIMIT 1)
    ";
    
    $uid = $pdo->query($sql)->fetchColumn();
    
    if(!$uid) {
        $notify->sendError("No DeadDrive");
    }
    
    if(isset($_GET['type']) && $_GET['type'] == "secure"){
        echo json_encode([['server_name' => "DeadDrive", "link" => "https://deaddrive.icu/embed/$uid"]]);
        exit;
    }
    
    $links = fetch("https://napi.deaddrive.icu/file/$uid");
    
    $links = json_decode($links,true);
    echo json_encode($links['servers']);
    
    exit;
}


if(isset($_GET['dumyid'])) {
    
    $dumy_id = $_GET['dumyid'];
    
    if(isset($_GET['limit']) && $_GET['limit'] == 1) {
        
        $ddrive = $pdo->query("SELECT movieLinks.*,Links_info.uid FROM movieLinks JOIN Links_info ON Links_info.Id = movieLinks.movie_drive_id JOIN Animes ON Animes.anime_id = movieLinks.anime_id WHERE Animes.anime_id = $dumy_id ORDER BY movieLinks.size DESC LIMIT 1")->fetchAll();
        $ddrive['deaddrive'] = $deaddrive['watch'] . $ddrive[0]['uid'];
        $playerx = $pdo->query("SELECT playerx FROM deadstream_playerx WHERE playerx != 'error' AND status = 1 AND is_episode = 0 AND cor_id = $dumy_id")->fetch();
        
        if($playerx) {
            
            $playerx['playerx'] = $playerxD . $playerx['playerx'] . "/";
            $res = array_merge($ddrive,$playerx);
        } else {
            
            $res = $ddrive;
            
        }
        
        
    } else {
        
        $res = $pdo->query("SELECT movieLinks.*,Links_info.uid FROM movieLinks JOIN Links_info ON Links_info.Id = movieLinks.movie_drive_id JOIN Animes ON Animes.anime_id = movieLinks.anime_id WHERE Animes.anime_id = $dumy_id ORDER BY movieLinks.size $order")->fetchAll();
    }
    
}

else if(isset($_GET['animeid'])) {
    
    $anime_id =  $_GET['animeid'];
    $res = $pdo->query("SELECT movieLinks.*,Links_info.uid FROM movieLinks JOIN Links_info ON Links_info.Id = movieLinks.movie_drive_id WHERE anime_id = $anime_id ORDER BY movieLinks.size $order")->fetchAll();
    
} elseif(isset($_GET['slug'])) {
    
    $slug =  $_GET['slug'];
    $res = $pdo->query("SELECT movieLinks.*,Links_info.uid FROM movieLinks JOIN Links_info ON Links_info.Id = movieLinks.movie_drive_id JOIN Animes ON Animes.anime_id = movieLinks.anime_id WHERE Animes.slug = '$slug' ORDER BY movieLinks.size $order")->fetchAll();
    
}

if(!isset($_GET['limit'])) {
    
    foreach($res as &$quality) {
        
        $quality['deaddrive'] = $deaddrive['download'] . $quality['uid'];
        $quality['embed'] = $deaddrive['embed'] . $quality['uid'];
        $quality['encoded'] = AES("encrypt",$quality['deaddrive']);
        $quality['redirect'] = "/redirect?url=" . $quality['encoded'];
        
    }
                
}

if(!$res) {
    $notify->sendError("No Movie Links");
}

echo json_encode($res);