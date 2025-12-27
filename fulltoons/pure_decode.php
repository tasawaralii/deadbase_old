<?php

include('simple_html_dom.php');

require('db.php');
require('Functions.php');

$sql = $pdo->query("SELECT slug FROM `katmoviehd` WHERE `body` = '' LIMIT 100;");
$ar = $sql->fetchAll(PDO::FETCH_ASSOC);

// print_r($ar);
// exit;


foreach($ar as $res) {
$slug = $res['slug'];
// $imgslug = $res['slug'].".jpg";


echo  $o = "https://katmoviehd.boo/".$res['slug'];

// // $html = str_get_html($res['body']);
$html = @file_get_html($o);

if($html != null) {

$body = $html->find('.entry-content', 0);
echo $title = $html->find('title', 0)->innertext;

if(!$title){
    $pdo->query("UPDATE katmoviehd set body ='skip' where slug = '$slug'");
    continue;
}

// $img = $body->find('img.lazy.lazy-hidden' , 0);
// $link = $img->getAttribute('data-src');
// if(strpos($link , "/wp-content/uploads/") !== false) {
//     $link = "https://puretoonz.com".$link;
// }
// $img = file_get_contents($link);


// if($img === false) {
//     $imgslug = "error";
//     telegram("error");
// } else {
// $image_info = getimagesizefromstring($img);
// $path = "images/".$res['slug'].".jpg";
// $save = file_put_contents($path , $img);

// // header('Content-Type: ' . $image_info['mime']);
// // echo $img;
// // }


// // $i = 0;
// // foreach($links as $a) {
// //     if(strpos($a->href ,"dl.php?url=") !== false && $i <= 50) {
// //     $b =  "https://puretoonz.com".$a->href;
// //     $c = file_get_html($b);
// //     $c = $c->find('meta', 0)->content;
// //     $c = str_replace("0;url=", '', $c);
// //     $a->href = $c;
// //     $i++;
// //     }
// }



$st = $pdo->prepare("UPDATE `katmoviehd` set `body` = :body, title = :title WHERE slug = :slug");
$st->bindParam(':body', $body);
$st->bindParam(':title', $title);
$st->bindParam(':slug', $slug);
$st->execute();
telegram($title);
    } else {
            $pdo->query("UPDATE katmoviehd set body ='skip' where slug = '$slug'");
    continue;
    }
}
?>