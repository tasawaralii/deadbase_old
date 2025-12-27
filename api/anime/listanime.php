<?php

require('auth.php');

require("../../db.php");
header("Content-type: application/json");


$where = false;

$completed = "";
$type = "";
$sort = "last_modified";

$limit = (isset($_GET['limit']) ? $_GET['limit'] : 12);
$offset = (isset($_GET['offset']) ? $_GET['offset'] : 0);

if(isset($_GET['completed'])) {
    
    if($_GET['completed'] == "true")
        $completed = "WHERE completed = 1";
    else
        $completed = "WHERE completed = 0";
    
    $where = true;
}

if(isset($_GET['type'])) {
    if(!$where) {
        $type = "WHERE type = '". $_GET['type'] ."'";
        $where = true;
    } else {
        $type = "AND type = '". $_GET['type'] ."'";
    }
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

$count_where = "";

if(isset($_GET['alpha'])) {
    
    $alpha = $_GET['alpha'];
    
    if($alpha == "0-9") {
    
    
        $res = $pdo->query("SELECT * FROM deadstream WHERE anime_name NOT regexp '^[a-zA-Z]+' ORDER BY $sort DESC LIMIT $limit OFFSET $offset")->fetchAll();
        
        $count_where = "WHERE anime_name NOT regexp '^[a-zA-Z]+'";
        
    } else {
        
        $res = $pdo->query("SELECT * FROM deadstream WHERE anime_name LIKE '$alpha%' ORDER BY $sort DESC LIMIT $limit OFFSET $offset")->fetchAll();
        
        $count_where = "WHERE anime_name LIKE '$alpha%'";
        
    }
    
} else {
    
    $res = $pdo->query("SELECT * FROM deadstream $completed $type ORDER BY $sort DESC LIMIT $limit OFFSET $offset")->fetchAll();
    
}

if(isset($_GET['count']) && $_GET['count'] == "true") {
    $count = $pdo->query("SELECT COUNT(*) AS total FROM deadstream $count_where")->fetchAll();
    
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