<?php

require("db.php");

header("Content-type: Application/Json");


$pdo->query("UPDATE movieLinks m JOIN (SELECT max(size) as size,anime_id FROM movieLinks GROUP BY anime_id) m1 ON m.anime_id = m1.anime_id AND m.size = m1.size SET m.isStream = 1");

$pdo->query("UPDATE EpisodeLinks l
INNER JOIN (
    SELECT episode_id, MAX(quality_order) AS max_size
    FROM EpisodeLinks
    WHERE quality_order != 8
    GROUP BY episode_id
) max_links
ON l.episode_id = max_links.episode_id AND l.quality_order = max_links.max_size
SET isStream = 1");


$streamLinks = $pdo->query("SELECT l.link_id,li.uid FROM EpisodeLinks l JOIN Links_info li ON li.Id = l.drive_id WHERE l.isStream AND l.isStreamReady = 0 ORDER BY l.link_id DESC LIMIT 500")->fetchAll();

$output = [];

foreach ($streamLinks as $l) {
    $url = "https://deaddrive.icu/crons/animeworld-stream.php?uid=" . $l['uid'];
    
    $ch = curl_init();
    
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response,true);
    $output[] = $response;
    
    if($response['status'] == "success") {
        if($response['already']) {
            $pdo->query("UPDATE EpisodeLinks SET isStreamReady = 1 WHERE link_id = ".$l['link_id']);
            // echo $l['link_id'];
        }
    }
    
}


$movieStreamingLinks = $pdo->query("SELECT m.movie_id,li.uid FROM movieLinks m JOIN Links_info li ON li.Id = m.movie_drive_id WHERE m.isStream AND m.isReadyStream = 0")->fetchAll();

foreach ($movieStreamingLinks as $l) {
    $url = "https://deaddrive.icu/crons/animeworld-stream.php?uid=" . $l['uid'];
    
    $ch = curl_init();
    
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response,true);
    $output[] = $response;
    
    if($response['status'] == "success") {
        if($response['already']) {
            $pdo->query("UPDATE movieLinks SET isReadyStream = 1 WHERE movie_id = ".$l['movie_id']);
        }
    }
    
}



echo json_encode($output);