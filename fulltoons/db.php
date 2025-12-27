<?php
$conn = new mysqli('localhost', 'deadbase', '6@7A8a9a', 'deadbase');

$db_host = 'localhost';
$db_name = 'deadbase';
$db_user = 'deadbase';
$db_pass = '6@7A8a9a';

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
?>