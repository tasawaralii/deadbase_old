<?php
require('db.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $a = $_POST;
    $anime_id = $a['anime'];
    
    if ($a['genre'] == '') {
        $malid = $a['mal'];
        
       $mal = json_decode(file_get_contents("https://api.jikan.moe/v4/anime/$malid/full"), true);
       print_r($age = explode(' - ', $mal['data']['rating']));
       if($age[0] == 'R') {
       $age[0] = 'R -';
       }
       $ageid = $pdo->query("SELECT age_id FROM age_ratings WHERE age_name = '".$age[0]."'")->fetch(PDO::FETCH_COLUMN);
       if($ageid) {
       $pdo->query("UPDATE Animes SET age_id = $ageid where anime_id = $anime_id");
       } else {
           echo "NO Age";
           exit;
       }
       echo $ageid;
       $pdo->query("INSERT INTO `anime_source`(`anime_id`, `source_id`, `external_id`) VALUES ('$anime_id','2','$malid')");
       foreach($mal['data']['genres'] as $g){
          genre($pdo, $g['name'], $anime_id);
       }
    } else {
        $genre = explode(',', $a['genre']);
        foreach ($genre as $g) {
            genre($pdo, $g, $anime_id);
        }
    }
}

function genre($pdo, $g, $anime_id) {
    $genre = $pdo->query("SELECT * FROM genres where name = '$g'")->fetch(PDO::FETCH_ASSOC);
    if($genre) {
        $pdo->query("INSERT IGNORE INTO anime_genres (anime_id, genre_id) VALUES ($anime_id,".$genre['id']." )");
    } else {
        $pdo->query("INSERT INTO genres (name) VALUES ('$g')");
        genre($pdo, $g, $anime_id);
    }
}

$a = $pdo->query("SELECT a.*
FROM Animes a
LEFT JOIN anime_genres ag ON a.anime_id = ag.anime_id
WHERE ag.genre_id IS NULL LIMIT 1;")->fetch(PDO::FETCH_ASSOC);

// print_r($a);
?>
<form action="" method="post">
    <?php echo $a['anime_name']; ?>
    <hr>
    <input type="hidden" name="anime" value="<?php echo $a['anime_id']; ?>">
    Anime ID: <?php echo $a['anime_id']; ?>
    <hr>
    MAL ID:
    <input name="mal">
    <hr>
    Genre :
    <input type="text" name="genre">
    <input type="submit">
</form>
