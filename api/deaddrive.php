<?php

require("../db.php");
header("Content-Type: application/json");

$ep = $pdo->query("SELECT EpisodeLinks.drive_id 
                    FROM EpisodeLinks 
                    LEFT JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id 
                    WHERE Links_info.Id IS NULL")->fetchAll(PDO::FETCH_COLUMN); 
                    
$pack = $pdo->query("SELECT Packs.drive_id 
                    FROM Packs 
                    LEFT JOIN Links_info ON Packs.drive_id = Links_info.Id 
                    WHERE Links_info.Id IS NULL")->fetchAll(PDO::FETCH_COLUMN);
                    
$movie = $pdo->query("SELECT movieLinks.movie_drive_id 
                    FROM `movieLinks` 
                    LEFT JOIN Links_info ON movieLinks.movie_drive_id = Links_info.Id 
                    WHERE Links_info.Id IS NULL;")->fetchAll(PDO::FETCH_COLUMN);



        $que = array_merge($ep, $pack, $movie);

        echo json_encode($que);        