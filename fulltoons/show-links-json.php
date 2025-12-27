<?php
require_once('db.php');
if(isset($_GET['slug']) && isset($_GET['season'])) {
$anime_slug = $_GET['slug'];
$season = $_GET['season'];
$episodes = [];
if(isset($_GET['hinOnly'])) {
$hindi_only = $_GET['hinOnly'];
} else {
$hindi_only = 0; }
$previousEpisodeNum = null;
$skippedEpisodes = [];
$query = "SELECT DISTINCT epSort, tmdb_id, ep_num, episode_id, episode_name FROM Episodes WHERE my_season_id = (SELECT my_season_id FROM my_seasons WHERE anime_id = (select anime_id from Animes where slug = '$anime_slug') AND season_num = $season) AND episode_id IN (SELECT episode_id FROM EpisodeLinks)";
$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) != 0 ){
while ($row = mysqli_fetch_assoc($result)) {
$currentEpisodeNum = $row['ep_num'];
    if ($previousEpisodeNum !== null && $currentEpisodeNum - $previousEpisodeNum > 1) {
        for ($i = $previousEpisodeNum + 1; $i < $currentEpisodeNum; $i++) {
            $skippedEpisodes[] = $i;
          }
      }
      $previousEpisodeNum = $currentEpisodeNum;
$epname = str_replace('\\','',$row['episode_name']);
$epname = preg_replace("/Episode \d+: /", '', $epname);
   $ep_id = $row['episode_id'];
$ep_query = "SELECT EpisodeLinks.drive_id, EpisodeLinks.quality, Links_info.size 
FROM EpisodeLinks 
JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id 
WHERE EpisodeLinks.episode_id = $ep_id AND Hindi_Only = $hindi_only
ORDER BY EpisodeLinks.quality_order ASC";
$ep_result = mysqli_query($conn, $ep_query);
$download = [];
while ($ep_row = mysqli_fetch_assoc($ep_result)) {
$download[] = array('quality' => $ep_row['quality'] , 'size' => $ep_row['size'] , 'drive_id' => $ep_row['drive_id']);
 }
$episodes[] = array('tmdb_number' => $row['ep_num'], 'my_number' => $row['epSort'], 'name' => $epname,'tmdb_id' => $row['tmdb_id'], 'download' => $download);
} 
$pack_query = "SELECT drive_id, quality, size FROM Packs WHERE my_season_id = (SELECT my_season_id FROM my_seasons WHERE anime_id = (select anime_id from Animes where slug = '$anime_slug') AND season_num = $season) ORDER BY `Packs`.`size` ASC;";
$pack_result = mysqli_query($conn, $pack_query);
$pack = [];
if (mysqli_num_rows($pack_result) > 0) {
while ($pack_row = mysqli_fetch_assoc($pack_result)) {
$pack[] =  array('quality' => $pack_row['quality'] , 'size' => $pack_row['size'] , 'drive_id' => $pack_row['drive_id']);
       }
    }
$name = ucwords(str_replace("-", " ", $anime_slug));
$array = array('status' => 'true', 'name' => $name, 'season' => $season, 'episodes' => $episodes, 'skipped' => $skippedEpisodes, 'pack' => $pack);

mysqli_close($conn);
         } else {
    $array = array('status' => 'false', 'message' => 'This Season does not exist in DeadBase', 'name' => 'Not Available', 'season' => '404');
   } 
       }
   else {
        $array = array('status' => 'false', 'message' => 'No Season is Selected', 'name' => 'Not Available', 'season' => '404');
       }

$json = json_encode($array, JSON_PRETTY_PRINT);
print_r($json);
?>