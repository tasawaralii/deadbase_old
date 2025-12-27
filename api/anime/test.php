<?php

require('auth.php');

require("../../db.php");
header("Content-Type: application/json; charset=UTF-8");


$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$completed = isset($_GET['completed']) ? filter_var($_GET['completed'], FILTER_VALIDATE_BOOLEAN) : null;
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$alpha = isset($_GET['alpha']) ? trim($_GET['alpha']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'last_modified';

$order = (isset($_GET['order']) && $_GET['order'] == "asc") ? 'ASC' : 'DESC';

$allowed_sorts = [
    'new' => 'links_update',
    'today' => 'today_views',
    'week' => 'last_7days',
    'month' => 'last_30days',
    'popular' => 'total_views',
    'last_modified' => 'last_modified',
    'random' => 'RAND()',
    'name' => 'anime_name'
];

$sort_column = in_array($sort, array_keys($allowed_sorts)) ? $allowed_sorts[$sort] : 'last_modified';


$conditions = [];
$params = [];


if ($completed !== null) {
    $conditions[] = 'completed = :completed';
    $params[':completed'] = $completed ? 1 : 0;
}


if ($type !== '' && ctype_alpha($type)) {
    $conditions[] = 'type = :type';
    $params[':type'] = $type;
}


$alpha_condition = '';


if ($alpha !== '') {
    if ($alpha === '0-9') {
        $alpha_condition = "anime_name NOT REGEXP '^[a-zA-Z]+'";
    } elseif (ctype_alpha($alpha) && strlen($alpha) === 1) {
        $conditions[] = 'anime_name LIKE :alpha';
        $params[':alpha'] = $alpha . '%';
    }
}


$where_clause = '';
if (!empty($conditions) || $alpha_condition !== '') {
    $where_clause = 'WHERE ' . ($alpha_condition ?: implode(' AND ', $conditions));
    if ($alpha_condition && !empty($conditions)) {
        $where_clause .= ' AND ' . implode(' AND ', $conditions);
    }
}

try {

    $count_sql = "SELECT COUNT(*) FROM Animes $where_clause";
    
    $stmt = $pdo->prepare($count_sql);
    
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();


    $sql = "SELECT * FROM deadstream $where_clause ORDER BY $sort_column $order LIMIT :limit OFFSET :offset";
    if ($sort_column === 'RAND()') {
        $sql = "SELECT * FROM deadstream $where_clause ORDER BY $sort_column LIMIT :limit OFFSET :offset";
    }
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $res = [
            'posts' => $animes,
            'total_posts' => $total,
            'current_page' => $page,
            'total_pages' => ceil($total/$limit)
        ];

    echo json_encode($res, JSON_THROW_ON_ERROR);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
} catch (JsonException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'JSON encoding error']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
    exit;
}