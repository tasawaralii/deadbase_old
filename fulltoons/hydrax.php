<?php
exit;
require('db.php');
require('Functions.php');
$sql = $pdo->query("SELECT Servers.Id,Links_info.Name FROM `Servers` JOIN Links_info ON Links_info.Id = Servers.Id WHERE Vidstream = '' and Links_info.user = 2 and Links_info.type != 'zip' and Servers.live != 0 ORDER BY Links_info.new_date DESC LIMIT 20");
$res = $sql->fetchAll(PDO::FETCH_ASSOC);

foreach($res as $r) {
    
   $playerx = playerx("https://drive.google.com/file/d/".$r['Id'], $r['Name']);
   if($playerx == "continue") {
       continue;
   }
   if($playerx == "404") {
       $pdo->query("UPDATE Servers SET live = 0 WHERE Id = '{$r['Id']}'");
       echo $r['Id']."<hr>";
       continue;
   }
   $pdo->query("UPDATE Servers Set Vidstream = '$playerx' WHERE Servers.Id = '{$r['Id']}'");
  print_r($playerx);
  echo "<hr>";
   telegram($r['Name']);
}



    function playerx($url , $name) {
        $req = file_get_contents("https://www.playerx.stream/api.php?api_key=jkA8NrSmZgWsFVlJ&url=".$url."&action=add_remote_url");
        $res = json_decode($req, true);
        if($res['result'] == true){
            $slug = str_replace(['https://vectorx.top/v/'], [''],  $res['player']);
            // print_r($res);
            return $slug;
        }
    }

?>