<?php

require('auth.php');

require("../../db.php");

header("Content-type: application/json");

if(isset($_GET['genre'])) {
    
    $limit = (isset($_GET['limit']) ? $_GET['limit'] : 12);
    $offset = (isset($_GET['offset']) ? $_GET['offset'] : 0);

    $genre = $_GET['genre'];
    
    $res = $pdo->query("SELECT deadstream.* FROM deadstream JOIN anime_genres ON anime_genres.anime_id = deadstream.anime_id
                        JOIN genres ON genres.id = anime_genres.genre_id WHERE genres.slug = '$genre' LIMIT $limit OFFSET $offset")->fetchAll();
                        


    if(isset($_GET['count']) && $_GET['count'] == "true") {
        
        $count = $pdo->query("SELECT COUNT(*) AS total FROM deadstream JOIN anime_genres ON anime_genres.anime_id = deadstream.anime_id
                        JOIN genres ON genres.id = anime_genres.genre_id WHERE genres.slug = '$genre'")->fetchAll();
        
        $animes = $res;
        $res = [];
        $res['animes'] = $animes;
        $res['total'] = $count;
        
    }
    
}

else if(isset($_GET['anime'])) {
    $anime_id = $_GET['anime'];
    $res = $pdo->query("SELECT genres.* FROM genres JOIN anime_genres ON anime_genres.anime_id = $anime_id AND anime_genres.genre_id = genres.id")->fetchAll();
} else {
    
    $res = $pdo->query("SELECT g.* FROM genres g JOIN anime_genres ag ON ag.genre_id = g.id")->fetchAll();
    
}

            
echo json_encode($res);