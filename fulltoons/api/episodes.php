<?php
$pdo = new PDO("mysql:host=localhost;dbname=fulltoon_Anime", "fulltoon_anime", "6@7A8a9a");


$a = $_GET['a'];
$s = $_GET['s'];
$e = $_GET['e'];
$q = "SELECT 
Animes.*,
source.*,
Episodes.epSort, Episodes.episode_name,Episodes.note,Episodes.img, Episodes.air_date,Episodes.overview, Episodes.note,Episodes.credit,
EpisodeLinks.quality, EpisodeLinks.quality_order,EpisodeLinks.Hindi_Only,EpisodeLinks.note,
Links_info.size,Links_info.uid,
(SELECT MAX(epSort) FROM Episodes JOIN EpisodeLinks on EpisodeLinks.episode_id = Episodes.episode_id JOIN my_seasons ON my_seasons.my_season_id = Episodes.my_season_id JOIN Animes ON Animes.anime_id = my_seasons.anime_id WHERE Animes.slug = '$a' AND my_seasons.my_season_num = $s AND Episodes.epSort < $e) As prev_episode_num,
(SELECT MIN(epSort) FROM Episodes JOIN EpisodeLinks on EpisodeLinks.episode_id = Episodes.episode_id JOIN my_seasons ON my_seasons.my_season_id = Episodes.my_season_id JOIN Animes ON Animes.anime_id = my_seasons.anime_id WHERE Animes.slug = '$a' AND my_seasons.my_season_num = $s AND Episodes.epSort > $e) AS next_episode_num
FROM Episodes 
JOIN my_seasons ON my_seasons.my_season_id = Episodes.my_season_id 
JOIN Animes ON Animes.anime_id = my_seasons.anime_id
JOIN source ON Animes.source = source.source_id
JOIN EpisodeLinks on Episodes.episode_id = EpisodeLinks.episode_id
JOIN Links_info ON Links_info.Id = EpisodeLinks.drive_id
WHERE Animes.slug = '$a' AND my_seasons.my_season_num = $s AND Episodes.epSort = $e ORDER BY EpisodeLinks.quality_order DESC;";
$lsq = $pdo->query($q);
$res = $lsq->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($res);

?>