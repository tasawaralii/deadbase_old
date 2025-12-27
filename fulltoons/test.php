<?php
exit;
set_time_limit(1500);
include('simple_html_dom.php');
require('db.php');
require('Functions.php');

$allposts = $pdo->query("SELECT slug FROM `pt-posts` WHERE title = ''  ORDER BY `pt-posts`.`date` DESC LIMIT 20");

$allres = $allposts->fetchAll(PDO::FETCH_ASSOC);

foreach ($allres as $spost)

{
$data = [];
$ptpost = $spost['slug'];
// $path = parse_url($ptpost);
// $path = trim($path['path'], '/');
$path = $ptpost;



// $checkdup = $pdo->query("SELECT slug from `pt-posts` WHERE slug = '$path'");
// $dupres = $checkdup->fetch(PDO::FETCH_ASSOC);
// if($dupres){
//     echo "Already";
//     exit;
// }


$data['slug'] = $path;







$html = file_get_html("https://puretoonz.com/".$ptpost);

$date = $html->find('.posted-on time', 0)->text();
$date = date('Y-m-d', strtotime($date));

$data['date'] = $date;

$title = str_replace(" | PureToons.Com", '',$html->find('title', 0)->text());
$data['title'] = $title;

$post_body = $html->find('.entry-content', 0); // Get the first element with the class ".entry_content"

if($post_body) {
    
    // $img = $post_body->find('.separator a', 0)->href;
    // if(strpos($img, "https://i.postimg.cc/" === false)) {
    //     $img = "https://puretoonz.com".$img;
    // }
    // $img = file_get_contents($img);
    // $save = file_put_contents("images/".$path.".jpg" , $img);
    // $data['img'] = $path.".jpg";
    
    $post_body->find('.separator', 0)->outertext = '';
    $elementsToRemove = $post_body->find('.crp_related');
    foreach ($elementsToRemove as $element) {
        $element->outertext = '';
    }
    // Create a new HTML object with the updated content
    $updated_html = str_get_html($post_body->outertext);
    // Find all <a> elements within $post_body
    // $links = $updated_html->find('a');
    // foreach ($links as $link) {
    //     // echo strpos($link->href ,"dl.php?url=")."  ".$link->href."<hr>";
    //     if(strpos($link->href ,"dl.php?url=") !== false) {
            
    //         $req = "https://puretoonz.com/".$link->href;
    //         $l = file_get_contents($req);
    //     $dom = new DOMDocument();
    //     $dom->loadHTML($l);
    //     $meta = $dom->getElementsByTagName('meta');
    //         $url = '';
    // foreach($meta as $mt) {
    // if ($mt->getAttribute('http-equiv') === 'refresh') {
    //     $content = $mt->getAttribute('content');
    //     $url = explode("url=", $content)[1];
    //     break;
    //         }
    
    //     }  
    
    //         $link->href = $url;
    //     }
    
        
    // }

    $data['body'] =  $updated_html;
    // echo $data['body'];

}
// exit;
// Connect to your database
require('db.php'); // Adjust this according to your actual database connection method

// Prepare the SQL statement
$sql = $pdo->prepare("UPDATE `pt-posts` SET `title` = :title, `body` = :body WHERE `slug` = :slug");

// Bind parameters
$sql->bindParam(':title', $data['title']);
$sql->bindParam(':body', $data['body']);
// $sql->bindParam(':img', $data['img']);
$sql->bindParam(':slug', $data['slug']);

// Execute the query
if ($sql->execute()) {
    telegram("Updated ".$data['title']);
} else {
    echo "Error executing query: " . $sql->errorInfo()[2];
}

                }
?>
