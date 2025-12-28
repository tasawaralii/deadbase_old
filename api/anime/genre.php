<?php

require('auth.php');

require("../../db.php");

header("Content-type: application/json");

if(isset($_GET['genre'])) {
    
    $limit = (isset($_GET['limit']) ? (int)$_GET['limit'] : 12);
    $offset = (isset($_GET['offset']) ? (int)$_GET['offset'] : 0);

    $genre = $_GET['genre'];
    
    $stmt = $pdo->prepare("SELECT deadstream.* FROM deadstream JOIN anime_genres ON anime_genres.anime_id = deadstream.anime_id
                        JOIN genres ON genres.id = anime_genres.genre_id WHERE genres.slug = :genre LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetchAll();


    if(isset($_GET['count']) && $_GET['count'] == "true") {
        
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM deadstream JOIN anime_genres ON anime_genres.anime_id = deadstream.anime_id
                        JOIN genres ON genres.id = anime_genres.genre_id WHERE genres.slug = :genre");
        $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchAll();
        
        $animes = $res;
        $res = [];
        $res['animes'] = $animes;
        $res['total'] = $count;
        
    }
    
}

else if(isset($_GET['anime'])) {
    $anime_id = $_GET['anime'];
    $stmt = $pdo->prepare("SELECT genres.* FROM genres JOIN anime_genres ON anime_genres.anime_id = :anime_id AND anime_genres.genre_id = genres.id");
    $stmt->bindParam(':anime_id', $anime_id, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetchAll();
} else {
    
    $stmt = $pdo->prepare("SELECT DISTINCT g.* FROM genres g JOIN anime_genres ag ON ag.genre_id = g.id");
    $stmt->execute();
    $res = $stmt->fetchAll();
    
}

            
echo json_encode($res);