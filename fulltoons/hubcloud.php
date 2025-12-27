<?php
// exit;
require('db.php');
require('Functions.php');
$res = $pdo->query("SELECT * FROM movieLinks JOIN Servers on Servers.Id = movieLinks.movie_drive_id WHERE Servers.hubcloud = '' LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

if ($res) {
    $r = $res;
} else {
    $r = $pdo->query("SELECT * 
FROM Packs 
JOIN Servers ON Servers.Id = Packs.drive_id 
WHERE Servers.hubcloud IS NULL 
LIMIT 10;")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($r)) {
        $r = $pdo->query("SELECT * FROM EpisodeLinks JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id ORDER BY Links_info.new_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    }
}

foreach($r as $f) {
$id = (isset($f['movie_drive_id']) ? $f['movie_drive_id'] : $f['drive_id']);
$hub = file_get_contents("https://hubcloud.day/drive/shareapi.php?key=ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09&link_add=".$id);
$slug = json_decode($hub, true);
echo $s = $slug['data'];
echo $sql = "UPDATE Servers SET hubcloud = '$s' where Id = '$id'";
$pdo->query($sql);
echo $slug['name'];
telegram($slug['name']."\n\n https://deaddrive.xyz/file/$id");
}
?>