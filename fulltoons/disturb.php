<?php
require_once('db.php');
require_once('Functions.php');
if(isset($_GET['folder'])) {
$apiKey = "AIzaSyAYL0KQop5h9oZTXvMq0v_yUqyDNgFGdOc";
$folderId = extractFolderId($_GET['folder']);
$files = fetchFilesFromFolder($folderId, $apiKey);
// echo "<pre>";
// print_r($files);
    foreach ($files as $file) {
        upload($file, $pdo);
          }
    
}
if(isset($_GET['single'])) {
$links = $_GET['single'];
$linksArray = explode(',', $links);
    foreach ($linksArray as $file) {
    upload(extractFileId($file), $pdo);
    }
}

function upload($id, $pdo) {
if($_GET['type'] == "pack") {
upload_pack($id);
  } else if($_GET['type'] == "single"){
  upload_episode($id, $pdo);
   }
         }
         
         
         
         
function upload_pack($id) {
$conn = new mysqli('localhost', 'fulltoon_anime', '6@7A8a9a', 'fulltoon_Anime');
$season_num = $_GET['season'];
$anime_id = $_GET['animeId'];
$key = "AIzaSyAYL0KQop5h9oZTXvMq0v_yUqyDNgFGdOc";
    $fields = "name,size";
    $gurl = 'https://www.googleapis.com/drive/v3/files/' .$id . '?fields=' .$fields .'&key=' .$key;
    $gfile = file_get_contents($gurl);
    $gfjson = json_decode($gfile, true);
 echo $name = str_replace("'", '', $gfjson['name']);
 $size = $gfjson['size'];
   echo "<br>".formatSize($size)."<hr>";
if (preg_match('/\b(480p|720p BD x265|720p x265|720p 10Bit|720p HEVC|720p|1080p BD x265|1080p x265|1080p 10Bit|1080p HEVC|1080p)\b/', $name, $matches)) {
    $quality = $matches[1];
} else {
    $quality = "Unknown";
}
$pack_sql = "INSERT IGNORE INTO Packs (my_season_id, drive_id, quality, size) Values ((SELECT my_season_id FROM my_seasons WHERE anime_id = $anime_id AND season_num = $season_num), '$id', '$quality', '$size');";
$add_pack = mysqli_query($conn, $pack_sql);
    }
    
    
    
function upload_episode($id, $pdo) {
$conn = new mysqli('localhost', 'fulltoon_anime', '6@7A8a9a', 'fulltoon_Anime');
echo "<br>";
    $anime_id = $_GET['anime'];
    $key = "AIzaSyAYL0KQop5h9oZTXvMq0v_yUqyDNgFGdOc";
    $fields = "name,size";
    $gurl = 'https://www.googleapis.com/drive/v3/files/' .$id . '?fields=' .$fields .'&key=' .$key;
    $gfile = file_get_contents($gurl);
    $gfjson = json_decode($gfile, true);
 echo $name = str_replace("'", '', $gfjson['name']);

if (preg_match('/ E(\d{2,3})/', $name, $matches)) {
    $episode = $matches[1];
} else {
    $episode = "Unknown";
    exit;
}


       
if (preg_match('/\b(480p|720p BD x265|720p x265|720p 10Bit|720p HEVC|720p|1080p BD x265|1080p x265|1080p 10Bit|1080p HEVC|1080p)\b/', $name, $matches)) {
    $quality = $matches[1];
} else {
    $quality = "Unknown";
}
      
switch ($quality) {
    case '360p':
        $order = 1;
        break;
    case '480p':
        $order = 2;
        break;
    case '576p':
        $order = 3;
        break;
    case '720p':
        $order = 4;
        break;
    case '720p HEVC':
    case '720p 10Bit':
    case '720p x265':
    case '720p BD x265':
        $order = 5;
        break;
    case '1080p':
        $order = 6;
        break;
    case '1080p HEVC':
    case '1080p 10Bit':
    case '1080p x265':
    case '1080p BD x265':
        $order = 7;
        break;
    default:
        echo "Quality Not Found";
        exit;
}

echo "<br>Episode: $episode<br>";
echo "Quality: $quality<br>";
echo "Order: $order<br>";
$ep_sql = "SELECT Episodes.episode_id FROM `Episodes` Join Seasons on Episodes.season_id = Seasons.season_id Join Animes on Animes.anime_id = Seasons.anime_id WHERE Episodes.epSort = $episode and Animes.anime_id = $anime_id;";
# echo "<br>";
$res_ep = mysqli_query($conn, $ep_sql);
$ep_id = mysqli_fetch_assoc($res_ep);
$e_id = $ep_id['episode_id'];
if ($ep_id == '') {
    echo "No episode found";
    echo "<br>$ep_sql";
    die(); // This will stop the execution
}
$honly = $_GET['honly'];
$sql = "INSERT IGNORE INTO `EpisodeLinks`(`episode_id`, `drive_id`, `quality`, `quality_order`, `Hindi_Only`) VALUES ($e_id, '$id', '$quality', '$order', '$honly')";
echo "<hr>";
$res_sql = mysqli_query($conn, $sql);


$pdo->query("UPDATE Animes SET links_update = CURRENT_TIMESTAMP WHERE anime_id = {$_GET['anime']}");


    }
function fetchFilesFromFolder($folderId, $apiKey) {
    $apiUrl = "https://www.googleapis.com/drive/v3/files?q=%27{$folderId}%27+in+parents&fields=files(id,webViewLink,mimeType)&key={$apiKey}&orderBy=name";
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);
    $files = [];
    if (isset($data['files']) && count($data['files']) > 0) {
        foreach ($data['files'] as $file) {
            if ($file['mimeType'] != 'application/vnd.google-apps.folder') {
                $files[] = $file['id'];
            } else {
                $subfolderFiles = fetchFilesFromFolder($file['id'], $apiKey);
                if ($subfolderFiles !== false) {
                    $files = array_merge($files, $subfolderFiles);
                }
            }
        }
    }
    return $files;
}
function extractFolderId($url) {
    $pattern = '/\/drive\/folders\/([a-zA-Z0-9-_]+)\?/';
    preg_match($pattern, $url, $matches);

    return isset($matches[1]) ? $matches[1] : null;
}
function extractFileId($url) {
    $pattern = '/\/d\/(.*?)(\/|$)/';
    preg_match($pattern, $url, $matches);

    return isset($matches[1]) ? $matches[1] : null;
}
?>