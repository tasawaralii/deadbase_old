<pre>
<?php

require('db.php');
$sql = $pdo->query("SELECT `ep_tmdb_id` , COUNT(*) AS num
FROM Episodes WHERE season_id != 5
GROUP BY `ep_tmdb_id`
HAVING num > 1;");

$res = $sql->fetchAll(PDO::FETCH_ASSOC);

foreach ($res as $d) {
    // $i = $d['num'];
    //     for($j = 0; $j <= $i;$j++) {
            
    //     }
    
    
    $del = $pdo->query("DELETE
FROM Episodes
WHERE NOT EXISTS (
    SELECT 1 
    FROM EpisodeLinks 
    WHERE EpisodeLinks.episode_id = Episodes.episode_id
) 
AND ep_tmdb_id = {$d['ep_tmdb_id']}");

    // $del->execute();
    // exit;
    // fetchAll(PDO::FETCH_ASSOC);
    print_r($dres);
    echo "<hr>";
    
    


}