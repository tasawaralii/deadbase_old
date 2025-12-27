<?php
// Your database connection code here
require_once('db.php');
require_once('Functions.php');
$tmdb_id = $_GET['season_id'];

// Fetch Episodes for the selected Season
$query = "SELECT DISTINCT Episodes.ep_num, Episodes.episode_id, Episodes.episode_name FROM Episodes INNER JOIN Seasons ON Episodes.season_id = Seasons.season_id INNER JOIN EpisodeLinks ON Episodes.episode_id = EpisodeLinks.episode_id WHERE Seasons.tmdb_id = $tmdb_id";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
$name = str_replace('\\','',$row['episode_name']);
   echo "<p><center><strong>Episode {$row['ep_num']}: {$name}</strong></center></p>";
$ep_id = $row['episode_id'];
$ep_query = "SELECT drive_id,quality FROM EpisodeLinks WHERE episode_id = $ep_id ORDER BY `EpisodeLinks`.`quality_order` ASC";
$ep_result = mysqli_query($conn, $ep_query);
echo "<p><center><strong> [";
$seperator = 0;
while ($ep_row = mysqli_fetch_assoc($ep_result)) {
if ($seperator == 0) {
    $seperator = 1;
} else {
    echo "] - [";
}
 
   echo "<a href='https://drive.deadtoons.online/file/{$ep_row['drive_id']}' target='_blank'>{$ep_row['quality']}</a>";

}
echo "] </strong></center></p><hr>";

}
$pack_query = "SELECT Packs.drive_id, Packs.quality, Packs.size FROM Packs JOIN Seasons ON Packs.season_id = Seasons.season_id WHERE Seasons.tmdb_id = $tmdb_id ORDER BY `Packs`.`size` ASC";
$pack_result = mysqli_query($conn, $pack_query);
if (mysqli_num_rows($pack_result) > 0) {
echo "<h4><em style='color: #ff9900;'><center>||Pack Links||</center></em></h4><hr>";
while ($pack_row = mysqli_fetch_assoc($pack_result)) {
echo '<strong><center><a href=
https://drive.deadtoons.online/file/'.$pack_row['drive_id'].'>'.$pack_row['quality'].' - ['.formatSize($pack_row['size']).']</center></strong><hr>';
}
}
mysqli_close($conn);
?>