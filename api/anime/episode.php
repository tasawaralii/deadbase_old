<?php

require('auth.php');
require("../../db.php");
require("functions.php");
require("config.php");

header("Content-Type: application/json");

global $deaddrive;
$res = [];

try {

    $anime_id = $_GET['animeid'];
    $season_id = $_GET['seasonid'];
    $episode_id = $_GET['episodeid'];

    $sql = "
        SELECT e.*, awp.player_link
        FROM Episodes e
        JOIN my_seasons s ON s.my_season_id = e.my_season_id
        JOIN Animes a ON a.anime_id = s.anime_id
        LEFT JOIN animeworld_player awp ON awp.episode_id = e.episode_id
        WHERE e.episode_id = ?
            AND s.my_season_id = ?
            AND a.anime_id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$episode_id, $season_id, $anime_id]);
    $res = $stmt->fetch();

    // --- If episode found, process image ---
    if (!empty($res)) {
        $res['img'] = makeImgUrl("tmdb", $res['img'], "mid");
    } else {
        echo json_encode(['error' => 'Episode not found']);
        exit;
    }

    // --- Handle links if requested ---
    if (isset($_GET['links']) && $_GET['links'] === "true") {
        $episodeId = $res['episode_id'];

        if (isset($_GET['limit']) && $_GET['limit'] == 1) {
            $links = [];
            // DeadDrive
            $stmt = $pdo->prepare("SELECT * FROM EpisodeLinks JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id WHERE episode_id = ? ORDER BY EpisodeLinks.quality_order DESC LIMIT 1");
            $stmt->execute([$episodeId]);
            $links['deaddrive'] = $stmt->fetch();

            if (!empty($links['deaddrive'])) {
                $links['deaddrive']['embed'] = $deaddrive['watch'] . $links['deaddrive']['uid'];
            }

        } else {
            $order = (isset($_GET['order']) && $_GET['order'] === "desc") ? "ORDER BY EpisodeLinks.quality_order DESC" : "";
            $stmt = $pdo->prepare("SELECT * FROM EpisodeLinks JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id WHERE episode_id = ? $order");
            $stmt->execute([$episodeId]);
            $links = $stmt->fetchAll();

            foreach ($links as &$l) {
                $l['embed'] = $deaddrive['watch'] . $l['uid'];
                $l['download'] = $deaddrive['download'] . $l['uid'];
            }
        }

        $detail = $res;
        $res = [
            'detail' => $detail,
            'links' => $links,
        ];
    }

    echo json_encode($res);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
