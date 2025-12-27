<?php

require('db.php');

$animes = $pdo->query("SELECT * FROM Animes");

    foreach($animes as $a) {
        $anime_id = $a['anime_id'];
        $poster = $a['backdrop_img'];
            if($a['source'] == 2) {
            $pdo->query("UPDATE images SET image_source = 3 where anime_id = $anime_id");    
            }
        // $pdo->query("INSERT IGNORE INTO `images`(`anime_id`, `image_type`, `image_source`, `image_external_id`) VALUES ($anime_id,'backdrop', 1, '$poster')");
        
        echo "<br>";
    }