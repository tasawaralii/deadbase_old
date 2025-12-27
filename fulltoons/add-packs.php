<?php
require_once('db.php');
require_once('Functions.php');
if(isset($_POST['submit_pack'])) {
$anime_id = $_POST['anime_id'];
$season_id = $_POST['season_id'];
$key = "AIzaSyCH4I2OxQnocrzdiZLeLOSI6GOnkuUR-yI";
$folId = extractFolderId($_POST['f_url']);
$folurl = 'https://www.googleapis.com/drive/v3/files?q=%27' .$folId .'%27+in+parents&key='.$key.'&orderBy=name';
$fjson = file_get_contents($folurl);
$fdjson = json_decode($fjson, true);
$files = $fdjson['files'];
    foreach ($files as $file) {
    $id = $file['id'];
    $fields = "name,mimeType,shared,fileExtension,size,videoMediaMetadata";
    $gurl = 'https://www.googleapis.com/drive/v3/files/' .$id . '?fields=' .$fields .'&key=' .$key;
    $gfile = file_get_contents($gurl);
    $gfjson = json_decode($gfile, true);
   echo $name = str_replace("'", '', $gfjson['name']);
   $size = $gfjson['size'];
   echo "<br>".formatSize($size);
if (preg_match('/\b(480p|720p HEVC|720p|1080p HEVC|1080p)\b/', $name, $matches)) {
    $quality = $matches[1];
} else {
    $quality = "Unknown";
}

echo "<br>Quality: $quality<hr>";
$pack_sql = "INSERT INTO Packs (season_id, drive_id, quality, size) Values ('$season_id', '$id', '$quality', '$size')";
   $add_pack = mysqli_query($conn, $pack_sql);
    }
  
}

function extractFolderId($url) {
    $pattern = '/\/folders\/(.*?)(\/|$)/';
    preg_match($pattern, $url, $matches);

    return isset($matches[1]) ? $matches[1] : null;
}
$query_animes = "SELECT * FROM Animes";
$result_animes = mysqli_query($conn, $query_animes);
?>

<form method="post" action="">
    Select Anime:
    <select name="anime_id" id="animeSelect" onchange="loadSeasons()">
        <?php
        while ($row = mysqli_fetch_assoc($result_animes)) {
            echo "<option value='{$row['anime_id']}'>{$row['anime_name']}</option>";
        }
        ?>
    </select><br>

    <div id="seasonDiv">
        Select Season:
        <select name="season_id" id="seasons">
            <!-- Seasons will be loaded dynamically based on the selected Anime using JavaScript -->
        </select><br>
        Single <input type="text" name="url"><br>
        Folder: <input type="text" name="f_url"><br>
        <input type="submit" value="Add Episode" name="submit_pack">
    </div>
</form>

<script>
function loadSeasons() {
    var selectedAnimeId = document.getElementById("animeSelect").value;
  //  var selectedAnimeId = animeSelect.options[animeSelect.selectedIndex].value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_seasons.php?anime_id=" + selectedAnimeId, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("seasons").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
window.onload = function() {
    loadSeasons();
    }
</script>