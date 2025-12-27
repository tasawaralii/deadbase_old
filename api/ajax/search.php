<?php

if (!isset($_GET['query'])) {
    exit;
}

require("../../db.php");

header('Content-Type: application/json');

$query = '%' . $_GET['query'] . '%';

try {
    $stmt = $pdo->prepare("SELECT anime_name,slug,type FROM Animes WHERE anime_name LIKE :query LIMIT 5");
    $stmt->execute([':query' => $query]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
