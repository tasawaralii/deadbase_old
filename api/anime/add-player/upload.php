<?php

require("../../../db.php");

function fetchContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // echo "cURL Error: " . curl_error($ch);
        return false;
    }

    curl_close($ch);
    return $response;
}


// header("content-type: application/json");

$variables = file_get_contents("variables.json");
$variables = json_decode($variables,true);
$pdo->query("UPDATE deadstream_playerx SET playerx = 'error', status = 1 WHERE playerx = ''");
$processing = $pdo->query("SELECT * FROM deadstream_playerx WHERE playerx IS NOT NULL AND status = 0 ORDER BY checking_order ASC LIMIT 50")->fetchAll();

if($processing)
    echo "<strong>Processing</strong><br><br>";

    foreach($processing as $p) {
        
        echo $p['name'];
        echo " - ";
        
        $slug = $p['playerx'];
        $playerxUrl = "https://www.playerx.stream/api.php?slug=$slug&api_key=xPwmVHqpF63oskrn&action=detail_video";
        $res = fetchContent($playerxUrl);
        $res = json_decode($res,true);
        if($res['result']) {
            
            $variables['total']--;
            
            $langs = (isset($res['original']['audio_track']) ? $res['original']['audio_track'] : "");
            
            $pdo->query("UPDATE deadstream_playerx SET languages = '$langs',status = 1 WHERE playerx = '$slug'");
            
            echo "<strong>Done : $langs</strong>";
        
        } else {
            echo "<strong>N/A</strong>";
        }
        echo "<br>";
}


$queue = [];
$queue = $pdo->query("SELECT * FROM deadstream_playerx WHERE playerx IS NULL ORDER BY tid DESC LIMIT 30")->fetchAll(); // priority DESC

echo "<strong><br>Pending</strong><br><br>";

if(!$queue) {
    echo "Nothing Pending";
} else {
    
    foreach($queue as $q) {
        
        $drive_id = $q['drive_id'];
        $name = str_replace(" ","+",$q['name']) . ".mkv";
        
        $direct_url = "https://deaddrive.bigila1739.workers.dev/$drive_id/$name";
        
        $playerxUrl = "https://www.playerx.stream/api.php?api_key=xPwmVHqpF63oskrn&url=$direct_url&action=add_remote_url";
        // echo $playerxUrl;
        $res = fetchContent($playerxUrl);
        $res = json_decode($res,true);
        
        if($res['result']) {
            
            
            preg_match('/https:\/\/newer.stream\/v\/(.*)\//',$res['player'],$res);
            if(!isset($res[1])) {
                print_r($res);
            }
            $slug = $res[1];
            $pdo->query("UPDATE deadstream_playerx SET playerx = '$slug', checking_order = ". ++$variables['counter'] ." WHERE drive_id = '$drive_id'");
            $variables['total']++;
    
        }
        
        echo $name . " Added to playerx" .  "<br>"; 
        
}
    
}



   
$fp = fopen("variables.json", 'w');
fwrite($fp,json_encode($variables));
fclose($fp);
exit;

   
    
    // echo $playerxUrl;