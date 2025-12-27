<?php
require('db.php');
$res = $pdo->query("SELECT * FROM `pt-posts` WHERE img != ''")->fetchAll(PDO::FETCH_ASSOC);
// // print_r($res[0]);

foreach($res as $r) {
echo $path = "images/".$r['img'];
if(file_exists($path)) {
    echo " yes<br>";
continue;
} else {
    $pdo->query("UPDATE `pt-posts` set img = '' where id = {$r['id']}");
    echo " no<br>";
}
// exit;
}
?>