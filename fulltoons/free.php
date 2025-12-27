<?php
require_once('db.php');
$pdo = new PDO("mysql:host=localhost;dbname=fulltoon_Anime;", "fulltoon_anime", "6@7A8a9a");
require_once('Functions.php');
function encode($url) {
// return "https://links.deadtoons.online/?url=".base64_encode($url);
return $url;
}
$anime_id = $_GET['animeId'];
if($_GET['type'] == "tv") {
if($_GET['season'] == "all") {
$allSeason = "SELECT DISTINCT my_seasons.my_season_num,my_seasons.my_season_name,Animes.slug FROM my_seasons JOIN Animes ON Animes.anime_id = my_seasons.anime_id JOIN Episodes ON Episodes.my_season_id = my_seasons.my_season_id WHERE my_seasons.anime_id = $anime_id";
$allSRes = mysqli_query($conn, $allSeason);
while($allSRow = mysqli_fetch_assoc($allSRes)) {
$includeSeas = strpos($allSRow['season_name'], "Season");
if($includeSeas !== false) {
echo "<center><a href='https://deaddrive.xyz/episodes/".$allSRow['slug']."/season-".$allSRow['season_num']."'><strong>Season ".$allSRow['season_num']."</strong></a></center><hr>";
  } else {
  echo "<center><a href='https://deaddrive.xyz/episodes/".$allSRow['slug']."/season-".$allSRow['season_num']."'><strong>Season ".$allSRow['season_num']." (".$allSRow['season_name'].")</strong></a></center><hr>";
  
       }
    }
} else {
$season = $_GET['season'];
if(isset($_GET['hinOnly'])) {
$hindi_only = $_GET['hinOnly'];
} else {
$hindi_only = 0; }
$previousEpisodeNum = null;
$skippedEpisodes = [];
echo "<h4 style='color: #ff6600;text-align:center;'>[Season $season]</h4><hr>";
$query = "SELECT DISTINCT epSort, ep_num, episode_id, episode_name, img, no_hindi FROM Episodes WHERE my_season_id = (SELECT my_season_id FROM my_seasons WHERE anime_id = $anime_id AND my_season_num = $season) AND episode_id IN (SELECT episode_id FROM EpisodeLinks) ORDER BY Episodes.epSort ASC";
$sql1 = $pdo->query($query);
$result = $sql1->fetchAll(PDO::FETCH_ASSOC);
$images = $result;
shuffle($images);
$imgKeys = (count($result) > 8) ? array_rand($images, 8) : array_rand($images, count($result));

if (!is_array($imgKeys)) {
    $imgKeys = [$imgKeys];
}
echo "<strong style='color:red;'><center><h4>ScreenShots</strong></center></h4><br>";
foreach ($imgKeys as $imgkey) {
    if($images[$imgkey]['img'] == '')
    continue;
    echo "<img style=' display: block; margin: auto;' src='https://image.tmdb.org/t/p/w780{$images[$imgkey]['img']}'>";
}
echo "<hr><strong><center>Downloading Links</center></strog><hr>";
// print_r($result);
foreach($result as $row) {
$currentEpisodeNum = $row['ep_num'];
    if ($previousEpisodeNum !== null && $currentEpisodeNum - $previousEpisodeNum > 1) {
        for ($i = $previousEpisodeNum + 1; $i < $currentEpisodeNum; $i++) {
            $skippedEpisodes[] = $i;
        }
    }
    $previousEpisodeNum = $currentEpisodeNum;
$name = str_replace('\\','',$row['episode_name']);
$name = preg_replace("/Episode \d+: /", '', $name);
   echo "<p><center><strong>Episode {$row['epSort']}: {$name}".(($row['no_hindi'] == 1) ? " <b style='color:red'>(Hindi not Available)</b>" : "")."</strong></center></p>";
$ep_id = $row['episode_id'];
$ep_query = "SELECT EpisodeLinks.drive_id, EpisodeLinks.quality, Links_info.size 
FROM EpisodeLinks 
JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id 
WHERE EpisodeLinks.episode_id = $ep_id AND Hindi_Only = $hindi_only
ORDER BY EpisodeLinks.quality_order ASC";
$ep_result = mysqli_query($conn, $ep_query);
echo "<p><center><strong> [";
$seperator = 0;
while ($ep_row = mysqli_fetch_assoc($ep_result)) {
if ($seperator == 0) {
    $seperator = 1;
} else {
   echo "] - [";
}
 $url = "https://deaddrive.xyz/file/{$ep_row['drive_id']}";
 $url = encode($url);
  echo "<a href='{$url}' target='_blank'>{$ep_row['quality']}</a>";
}
echo "] </strong></center></p><hr>";

}
if (!empty($skippedEpisodes)) {
    if (count($skippedEpisodes) == 1) {
        echo "Note : <b>Episode " . $skippedEpisodes[0] . " is not available and will be added when available</b>";
    } else {
        echo "Note: <b>Following are some skipped episodes and will be added when available: " . implode(',', $skippedEpisodes) . "</b>";
    }
}


$sql = $pdo->prepare("SELECT CONCAT(Packs.startEp, '-', Packs.endEp) AS startEndEp, Packs.*, Animes.anime_name, my_seasons.my_season_num 
                       FROM Packs 
                       JOIN my_seasons ON Packs.my_season_id = my_seasons.my_season_id 
                       JOIN Animes ON my_seasons.anime_id = Animes.anime_id 
                       WHERE my_seasons.my_season_id = (
                           SELECT my_season_id 
                           FROM my_seasons 
                           WHERE anime_id = ? AND my_season_num = ?
                       ) 
                       AND honly = ? 
                       ORDER BY Packs.startEp ASC, Packs.endEp DESC, Packs.size ASC");

$sql->execute([$anime_id, $season, $hindi_only]);


$res = $sql->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
// echo "<pre>";
// print_r($res);
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
        echo "<strong><center><a href='${url}' target='_blank'>{$pack_row['anime_name']} S" . str_pad($pack_row['my_season_num'], 2, '0', STR_PAD_LEFT) . " {$pack_row['quality']} - [" . formatSize($pack_row['size']) . "]</a></center></strong><hr>";
    }
}


}
        } }
        else {
        $movie_query = "SELECT * FROM `movieLinks` WHERE anime_id = $anime_id ORDER BY `movieLinks`.`size` ASC";
$movie_result = mysqli_query($conn, $movie_query);
if (mysqli_num_rows($movie_result) > 0) {
echo "<h4><center>Movie Links</center></h4><hr>";
while ($movie_row = mysqli_fetch_assoc($movie_result)) {
$url = "https://deaddrive.xyz/file/{$movie_row['movie_drive_id']}";
$url = encode($url);
echo "<strong><center><a href='{$url}' target='_blank'>{$movie_row['quality']} - [" . formatSize($movie_row['size']) . "]</a></center></strong><hr>";
                
                }}}
          
mysqli_close($conn);
?>