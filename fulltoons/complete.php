<?php

require('db.php');

try {
    $sql = $pdo->prepare("SELECT * FROM Animes WHERE anime_rel_date = 0000-00-00 AND type != 'tv' LIMIT 10");
    $sql->execute();
    $res = $sql->fetchAll(PDO::FETCH_ASSOC);

    foreach ($res as $r) {
        $url = "https://api.themoviedb.org/3/{$r['type']}/{$r['tmdb_id']}?api_key=23191afa2b81389abd78b3e51bfc58fb";
        $info = json_decode(@file_get_contents($url), true);
        
        if (!$info) {
            throw new Exception("Failed to fetch data from API: $url");
        }

        // $img = $info['backdrop_path'];
        // $poster = $info['poster_path'];
        // $overview = $info['overview'];
        // // $runtime = $info['last_episode_to_air']['runtime'];
        // $runtime = $info['runtime'];
        // $rat = $info['vote_average'];
        // $slug = slugify($r['anime_name']);
        $date = $info['release_date'];
    echo $r['anime_name']."<hr>";
        $update = $pdo->prepare("UPDATE Animes SET anime_rel_date = ? WHERE tmdb_id = ?");
        $update->execute([$date, $r['tmdb_id']]);
    }

    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}


function slugify($string) {
        // Convert the string to lowercase
        $slug = strtolower($string);
        
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        // Remove consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        return $slug;
    }