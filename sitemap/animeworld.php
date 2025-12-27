<?php

require_once "../db.php";

$domain = "https://animeworld-india.me";
$xmlfilePath = "sitemap-animeworld.xml";


$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;


$fixedLinks = [
    ['slug' => $domain . "/privacy-policy", 'last_modified' => '2025-04-12', 'priority' => 1.0, 'frequency' => 'monthly'],
    ['slug' => $domain . "/dmca", 'last_modified' => '2025-04-12', 'priority' => 1.0, 'frequency' => 'monthly'],
];

foreach ($fixedLinks as $link) {
    $xml .= "  <url>" . PHP_EOL;
    $xml .= "    <loc>{$link['slug']}</loc>" . PHP_EOL;
    $xml .= "    <lastmod>{$link['last_modified']}</lastmod>" . PHP_EOL;
    $xml .= "    <changefreq>{$link['frequency']}</changefreq>" . PHP_EOL;
    $xml .= "    <priority>{$link['priority']}</priority>" . PHP_EOL;
    $xml .= "  </url>" . PHP_EOL;
}


$animes = $pdo->query("SELECT slug, type, DATE(links_update) as last_modified FROM Animes")->fetchAll();

foreach ($animes as $a) {
    $url = $domain . ($a['type'] == "tv" ? "/series/" : "/movies/") . $a['slug'];
    $frequency = ($a['type'] == "tv" ? "weekly" : "yearly");
    $priority = 0.8;

    $xml .= "  <url>" . PHP_EOL;
    $xml .= "    <loc>{$url}</loc>" . PHP_EOL;
    $xml .= "    <lastmod>{$a['last_modified']}</lastmod>" . PHP_EOL;
    $xml .= "    <changefreq>{$frequency}</changefreq>" . PHP_EOL;
    $xml .= "    <priority>{$priority}</priority>" . PHP_EOL;
    $xml .= "  </url>" . PHP_EOL;
}

$xml .= '</urlset>';


file_put_contents($xmlfilePath, $xml);

echo "Sitemap successfully generated!";
