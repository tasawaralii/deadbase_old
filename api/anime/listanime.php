<?php

require('auth.php');

require("../../db.php");
header("Content-type: application/json");


$sort = "last_modified";
$limit = (isset($_GET['limit']) ? $_GET['limit'] : 12);
$offset = (isset($_GET['offset']) ? $_GET['offset'] : 0);

$conditions = [];

if(isset($_GET['completed'])) {
    $conditions[] = ($_GET['completed'] == "true") ? "completed = 1" : "completed = 0";
}

if(isset($_GET['type'])) {
    $conditions[] = "type = '". $_GET['type'] ."'";
}

if(isset($_GET['alpha'])) {
    $alpha = $_GET['alpha'];
    if($alpha == "0-9")
        $conditions[] = "anime_name NOT regexp '^[a-zA-Z]+'";
    else
        $conditions[] = "anime_name LIKE '$alpha%'";
}

if(isset($_GET['sort'])) {
    if($_GET['sort'] == "new")
        $sort = "links_update";
    elseif($_GET['sort'] == "today")
        $sort = "today_views";
    elseif($_GET['sort'] == "week")
        $sort = "last_7days";
    elseif($_GET['sort'] == "month")
        $sort = "last_30days";
    elseif($_GET['sort'] == "popular")
        $sort = "total_views";
    elseif($_GET['sort'] == "random")
        $sort = "RAND()";
}

$whereClause = (count($conditions) > 0) ? "WHERE " . implode(" AND ", $conditions) : "";
$orderBy = ($sort === "RAND()") ? "ORDER BY RAND()" : "ORDER BY $sort DESC";

$res = $pdo->query("SELECT * FROM deadstream $whereClause $orderBy LIMIT $limit OFFSET $offset")->fetchAll();

if(isset($_GET['count']) && $_GET['count'] == "true") {
    $count = $pdo->query("SELECT COUNT(*) AS total FROM deadstream $whereClause")->fetchAll();
    
    $animes = $res;
    $res = [];
    $res['animes'] = $animes;
    $res['total'] = $count;
    
}

$json = json_encode($res);

if ($json === false) {
    echo json_last_error_msg(); // Display the JSON encoding error
} else {
    echo $json; // Output JSON if encoding is successful
}