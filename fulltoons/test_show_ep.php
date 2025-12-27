<?php
// Your database connection code here
require_once('db.php');
require_once('Functions.php');
$my_season_id = $_GET['season_id'];

// Fetch Episodes for the selected Season
$query = "SELECT DISTINCT ep_num, episode_id, episode_name FROM Episodes WHERE my_season_id = $my_season_id AND episode_id IN (SELECT episode_id FROM EpisodeLinks);";
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
 
  echo "<a href='https://drive.google.com/file/d/{$ep_row['drive_id']}' target='_blank'>{$ep_row['quality']}</a>";
}
echo "] </strong></center></p><hr>";

}
echo $pack_query = "SELECT drive_id, quality, size FROM Packs WHERE my_season_id = $my_season_id ORDER BY `Packs`.`size` ASC";
$pack_result = mysqli_query($conn, $pack_query);
if (mysqli_num_rows($pack_result) > 0) {
echo "<h4><center>Pack Links</center></h4>";
while ($pack_row = mysqli_fetch_assoc($pack_result)) {
echo '<strong><center><a href=
https://drive.google.com/file/d/'.$pack_row['drive_id'].'>'.$pack_row['quality'].' - ['.formatSize($pack_row['size']).']</center></strong><hr>';
}
}
mysqli_close($conn);
?>