<?php

$pdo = new PDO("mysql:host=localhost;dbname=fulltoon_Anime", "fulltoon_anime", "6@7A8a9a");
$slug = $_GET['slug'];
$res = $pdo->query("SELECT * FROM movieLinks
JOIN Animes ON Animes.anime_id = movieLinks.anime_id 
JOIN Links_info ON Links_info.Id = movieLinks.movie_drive_id 
Join source On source.source_id = Animes.source 
WHERE Animes.slug = '$slug'  ORDER BY `movieLinks`.`size` DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($res);

?>