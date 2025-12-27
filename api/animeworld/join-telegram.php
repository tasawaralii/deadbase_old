<?php

require('db.php');


if($_SERVER['REQUEST_METHOD'] != "POST") {
    header("HTTP/1.0 404 Not Found");
    exit;
}


if(!isset($_POST['ip'])) {die(json_encode(['status' => 'error', 'message' => "No Ip Given"]));}
if(!isset($_POST['api'])) {die(json_encode(['status' => 'error', 'message' => "No Api Given"]));}

$ip = $_POST['ip'];
$api = $_POST['api'];

if($api != base64_encode('h4r$h8')) {
    die(json_encode(['status' => 'error', 'message' => "Wrong Api"]));
}

if(checkIp($ip,$pdo)) {
    die(json_encode(['status' => 'success', 'message' => 'Eligible For Free', 'telegram' => false]));
} else {
    die(json_encode(['status' => 'success', 'message' => 'Not Eligible For Free', 'telegram' => true]));
}

function checkIp($ip,$pdo) {
    
    $pdo->query("DELETE FROM users_ips WHERE date_time < NOW() - INTERVAL 8 HOUR");

    $stmt = $pdo->prepare("SELECT 1 FROM users_ips WHERE ip_address = :ip");

    $stmt->execute([":ip" => $ip]);

    $exists = $stmt->fetch();

    if($exists)
        return true;
    
    allowIp($ip,$pdo);

    return false;
}

function allowIp($ip,$pdo) {

    $stmt = $pdo->prepare("INSERT INTO users_ips (ip_address, date_time) VALUES (:ip, NOW())");
    $stmt->execute([":ip" => $ip]);
    
}

