<?php

require('auth.php');

require("../../db.php");

header("Content-Type: application/json; charset=UTF-8");


$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : "";


$offset = ($page - 1) * $limit;


$searchTerm = '%' . $keyword . '%';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM Animes WHERE anime_name LIKE :term OR keywords LIKE :term");
$stmt->execute([':term' => $searchTerm]);
$total = (int)$stmt->fetchColumn();


$stmt = $pdo->prepare("SELECT * FROM deadstream WHERE anime_name LIKE :term OR keywords LIKE :term ORDER BY links_update DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':term', $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$animes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$res = [
    'total_posts' => $total,
    'total_pages' => ceil($total / $limit),
    'current_page' => $page,
    'posts' => $animes
];

echo json_encode($res, JSON_THROW_ON_ERROR);
