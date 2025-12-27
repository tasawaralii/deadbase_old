<?php
$conn = new PDO("mysql:host=localhost;dbname=fulltoon_Anime","fulltoon_anime","6@7A8a9a");
$sql = $conn->query("SELECT DISTINCT Animes.tmdb_id, Animes.anime_name, Seasons.season_num, Seasons.season_id 
FROM Animes 
JOIN Seasons ON Animes.anime_id = Seasons.anime_id 
JOIN Episodes ON Episodes.season_id = Seasons.season_id 
WHERE Animes.type = 'tv' 
AND Episodes.tmdb_id != 0 
AND Seasons.season_id NOT IN (
    SELECT DISTINCT e.season_id
    FROM Episodes e
    WHERE e.img != ''
)
LIMIT 10;
");
$res = $sql->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
// print_r($res);
// echo count($res);
foreach($res as $anime) {
    echo "<center><strong>{$anime['anime_name']}</center></strong><hr>";
    $tmdb = "https://api.themoviedb.org/3/tv/{$anime['tmdb_id']}/season/{$anime['season_num']}?api_key=23191afa2b81389abd78b3e51bfc58fb";
    $tmdbRes = json_decode(file_get_contents($tmdb), true);
    foreach($tmdbRes['episodes'] as $episodes) {
        // print_r($episodes);
        $air = $episodes['air_date'];
        $img = $episodes['still_path'];
        $overview = $episodes['overview'];
        $id = $episodes['id'];
        $sql = "UPDATE Episodes SET air_date = :air_date, img = :img, overview = :overview WHERE tmdb_id = :id";
        $stmt = $conn->prepare($sql);
    $stmt->bindParam(':air_date', $air);
    $stmt->bindParam(':img', $img);
    $stmt->bindParam(':overview', $overview);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    echo $episodes['name'];
        echo "<hr>";
    }
    
    
    // $epSql = $conn->query("");
}
?>