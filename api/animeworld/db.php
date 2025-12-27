<?php

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    
    $pdo = new PDO('mysql:host=localhost;dbname='. "deaddrive_animeworld", "deaddrive_animeworld", "6@7A8a9a" , $options);
    $pdo->query("SET time_zone = '+05:00'");

    // $pdo->exec("SET NAMES 'utf8mb4'");
    
?>
