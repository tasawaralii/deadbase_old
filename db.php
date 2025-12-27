<?php

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO('mysql:host=localhost;dbname=deadbase', 'deadbase', '6@7A8a9a', $options);
    $pdo->exec("SET NAMES 'utf8mb4'");
    

} catch (PDOException $e) {

    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
