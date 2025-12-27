<?php
require('Functions.php');
function encode($url) {
// return "https://links.deadtoons.online/?url=".base64_encode($url);
return $url;
}

$anime_id = $_GET['animeId'];
$season = $_GET['season'];
$hindi_only = $_GET['hinOnly'];
$conn = new PDO("mysql:host=localhost;dbname=fulltoon_Anime;", "fulltoon_anime", "6@7A8a9a");
$sql = $conn->prepare("SELECT CONCAT(Packs.startEp, '-', Packs.endEp) AS startEndEp, Packs.*, Animes.anime_name, my_seasons.season_num 
                       FROM Packs 
                       JOIN my_seasons ON Packs.my_season_id = my_seasons.my_season_id 
                       JOIN Animes ON my_seasons.anime_id = Animes.anime_id 
                       WHERE my_seasons.my_season_id = (
                           SELECT my_season_id 
                           FROM my_seasons 
                           WHERE anime_id = ? AND season_num = ?
                       ) 
                       AND honly = ? 
                       ORDER BY Packs.startEp ASC, Packs.endEp DESC, Packs.size ASC");

$sql->execute([$anime_id, $season, $hindi_only]);


$res = $sql->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
echo "<pre>";
print_r($res);
if (count($res)) {
    echo "<h3 style='color: #ff6600;'><center>[Pack Links]</center></h3><hr>";
    foreach ($res as $startEndEp => $packs) {
        list($startEp, $endEp) = explode("-", $startEndEp);
        $startEp = str_pad($startEp, 2, '0', STR_PAD_LEFT);
        $endEp = str_pad($endEp, 2, '0', STR_PAD_LEFT);
        echo "<strong><center> EP{$startEp} - EP{$endEp}</center></strong><hr>";
    foreach ($packs as $pack_row) {
        $url = "https://deaddrive.xyz/file/{$pack_row['drive_id']}";
        $url = encode($url);
        echo "<strong><center><a href='${url}' target='_blank'>{$pack_row['anime_name']} S" . str_pad($pack_row['season_num'], 2, '0', STR_PAD_LEFT) . " {$pack_row['quality']} - [" . formatSize($pack_row['size']) . "]</a></center></strong><hr>";
    }
}


}


?>