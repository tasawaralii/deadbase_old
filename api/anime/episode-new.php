<?php

header("Content-type: Application/json");

$ddrive_page = "https://new1.deaddrive.shop/file/";
$streaming_site_domain = "https://animeworld-india.me/series/";

require("telegram.php");
$notify = new Telegram();

// require('auth.php');
require("../../db.php");

if(isset($_GET['slug'])) {
    
    $slug = $_GET['slug'];
    $season_number = $_GET['season'];
    $episode_number = $_GET['episode'];
    
    $stmt = $pdo->prepare("SELECT e.* FROM Episodes e JOIN my_seasons ms ON ms.my_season_id = e.my_season_id JOIN Animes a ON a.anime_id = ms.anime_id WHERE a.slug = :slug AND ms.my_season_num = :snum AND e.epSort = :epnum");
    
    $stmt->execute([
        'slug' => $slug,
        'snum' => $season_number,
        'epnum' => $episode_number
    ]);
    
    $episodeInfo = $stmt->fetch();
    
    if(!$episodeInfo) {
        $notify->sendError("Episode Not Available : Deadtoons");
        die(json_encode(['status' => 'error', 'message' => 'Episode Not Available']));
    }
    
    $links = $pdo->query("SELECT el.quality,li.size,el.Hindi_Only,el.note, CONCAT('$ddrive_page',li.uid) AS download FROM EpisodeLinks el JOIN Links_info li ON el.drive_id = li.Id WHERE el.episode_id = " . $episodeInfo['episode_id'])->fetchAll();
    
    die(json_encode([
        'info' => $episodeInfo,
        'links' => $links,
        'streaming_url' => $streaming_site_domain . $slug
    ]));

    die("Error");
}


if(!isset($_GET['episodeid'])) {
    $notify->sendError("NO ID GIVEN");
    die(json_encode(['status' => 'error', 'message' => 'No Episode Id Given']));
    
}
    

$episodeid = $_GET['episodeid'];

$stmt = $pdo->prepare("SELECT uid FROM Links_info WHERE Id = (SELECT drive_id FROM EpisodeLinks WHERE episode_id = :episode_id AND isStream = 1 LIMIT 1)");
$stmt->execute(['episode_id' => $episodeid]);

$uid = $stmt->fetchColumn();

if(!$uid) {
    $notify->sendError("Deaddrive or Episode Not Available");
    die(json_encode(['status' => 'error', 'message' => 'Deaddrive or Episode Not Available']));
}


$ddrive = "https://napi.deaddrive.icu/file/$uid";
// echo $ddrive;

$links = file_get_contents($ddrive);

// print_r($links);

if(!json_decode($links,true)) {
    $notify->sendError("No servers Available");
}

echo $links;