<?php
require_once('db.php');
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
        $hindi_only = ((isset($_GET['hinOnly'])) ? $_GET['hinOnly'] : 0);


        $previousEpisodeNum = null;
        $skippedEpisodes = [];

        $sql = $pdo->query("
        
        SELECT DISTINCT 
        Animes.slug,Animes.tmdb_id ,
        my_seasons.my_season_num, 
        Episodes.episode_name, Episodes.epSort, Episodes.ep_num, Episodes.img, Episodes.no_hindi 
        FROM Episodes 
        Join my_seasons on my_seasons.my_season_id = Episodes.my_season_id JOIN 
        Animes on Animes.anime_id = my_seasons.anime_id 
        JOIN EpisodeLinks on Episodes.episode_id = EpisodeLinks.episode_id 
        Where 
        my_seasons.my_season_id = (SELECT my_season_id FROM `my_seasons` WHERE `anime_id` = $anime_id AND `my_season_num` = $season) 
        AND EpisodeLinks.Hindi_Only = $hindi_only
        
        ");
        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

        echo img($res);

                foreach($res as $row) {
                    
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
   echo '<center>';
   echo '<strong><a href="https://deadtoons.online/episode/'.$row['slug'].'/'.$row['my_season_num'].'x'.$row['epSort'].'">Download</a></strong>';
   echo ' - ';
   echo '<a href= "https://deadstream.xyz/'.$row['slug'].'-'.$row['tmdb_id'].'">Watch Online</a>';
   echo '</center>';
   echo "<hr>";





            }
            
    
if (!empty($skippedEpisodes)) {
    if (count($skippedEpisodes) == 1) {
        echo "Note : <b>Episode " . $skippedEpisodes[0] . " is not available and will be added when available</b>";
    } else {
        echo "Note: <b>Following are some skipped episodes and will be added when available: " . implode(',', $skippedEpisodes) . "</b>";
    }
        }        
            
            
$psql = $pdo->prepare("
    SELECT 
        CONCAT(Packs.startEp, '-', Packs.endEp) AS startEndEp, 
        Packs.*, 
        Animes.anime_name, 
        my_seasons.my_season_num 
    FROM Packs 
    JOIN my_seasons ON Packs.my_season_id = my_seasons.my_season_id 
    JOIN Animes ON my_seasons.anime_id = Animes.anime_id 
    WHERE my_seasons.my_season_id = (
        SELECT my_season_id 
        FROM my_seasons 
        WHERE anime_id = ? AND my_season_num = ?
    ) 
    AND honly = ? 
    ORDER BY Packs.startEp ASC, Packs.endEp DESC, Packs.size ASC
");

$psql->execute([$anime_id, $season, $hindi_only]);



$pres = $sql->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);

if (count($pres)) {
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
        }   
                
            } else {
        
        $movie_query = "SELECT * FROM `movieLinks` WHERE anime_id = $anime_id ORDER BY `movieLinks`.`size` ASC";
$movie_result = mysqli_query($conn, $movie_query);
if (mysqli_num_rows($movie_result) > 0) {
echo "<h4><center>Movie Links</center></h4><hr>";
while ($movie_row = mysqli_fetch_assoc($movie_result)) {
$url = "https://deaddrive.xyz/file/{$movie_row['movie_drive_id']}";
$url = encode($url);
echo "<strong><center><a href='{$url}' target='_blank'>{$movie_row['quality']} - [" . formatSize($movie_row['size']) . "]</a></center></strong><hr>";
                
                }}
        }



function img($res) {
    $html = '<center><h4><strong style="color:red;">ScreenShots</strong></h4></center>';
if(count($res) == 1) {
    $html .= '<img style="display:block;margin:auto;" src="https://image.tmdb.org/t/p/w780'.$res[0]['img'].'">';
} else {
$img = ((count($res) > 8) ? array_rand($res, 8) : array_rand($res, count($res)));

foreach ($img as $key) {
        if ($res[$key]['img'] == '')
        continue;
        $html.= '<img style="display:block;margin:auto;" src="https://image.tmdb.org/t/p/w780'.$res[$key]['img'].'">';
}
    }
    
    return $html."<hr>";
        }

?>