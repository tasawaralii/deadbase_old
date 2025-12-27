<?php
// Load the XML file

for($i = 8 ; $i<=29 ; $i++) {
    $xml = simplexml_load_file('https://katmoviehd.boo/post-sitemap'.$i.'.xml');


require('db.php'); // Adjust this according to your actual database connection method

// Loop through each <url> element in the sitemap
foreach ($xml->url as $url) {
    // Access and process the data within each <url> element
    $loc = $url->loc; // Access the <loc> element
    $slug = parse_url($loc);
    $slug = $slug['path'];
    $lastmod = $url->lastmod; // Access the <lastmod> element

    // Convert the last modified date to a MySQL-compatible format
    $date = date('Y-m-d H:i:s', strtotime($lastmod));
    // Insert or update the data in your database
    $sql = $pdo->prepare("INSERT IGNORE INTO `katmoviehd`(`slug`, `date`) VALUES (?, ?)");
    if ($sql->execute([$slug, $date])) {
        echo "Data inserted successfully"."<br>";
    } else {
        echo "Error inserting data: " . $sql->errorInfo()[2] . "\n";
    }
    
}
}
?>
