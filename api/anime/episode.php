<?php

require('auth.php');
require("../../db.php");
require("functions.php");
require("config.php");

header("Content-Type: application/json");

global $deaddrive;
$res = [];

try {

    // --- New DeadDrive Link ---
    if (isset($_GET['new'])) {
        $episode_id = intval($_GET['episodeid']);

        $sql = "SELECT uid FROM Links_info WHERE Id = (
                    SELECT drive_id FROM EpisodeLinks 
                    WHERE episode_id = ? AND isStream = 1 LIMIT 1
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$episode_id]);
        $uid = $stmt->fetchColumn();

        if (!$uid) {
            echo json_encode(['error' => 'No UID found']);
            exit;
        }

        if (isset($_GET['type']) && $_GET['type'] === "secure") {
            echo json_encode([[
                'server_name' => "DeadDrive",
                'link' => "https://deaddrive.icu/embed/$uid"
            ]]);
            exit;
        }

        echo fetch("https://api.deaddrive.icu/get-links?uid=$uid");
        exit;
    }

    // --- Fetch by animeid, season, and episode number ---
    if (isset($_GET['animeid'], $_GET['season'], $_GET['episode'])) {
        $anime_id = intval($_GET['animeid']);
        $season = intval($_GET['season']);
        $episode = intval($_GET['episode']);

        $sql = "SELECT Episodes.* 
                FROM Episodes 
                JOIN my_seasons ON my_seasons.my_season_id = Episodes.my_season_id 
                JOIN Animes ON Animes.anime_id = my_seasons.anime_id
                WHERE Animes.anime_id = ? AND my_seasons.my_season_num = ? AND Episodes.epSort = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$anime_id, $season, $episode]);
        $res = $stmt->fetchAll();
    }

    // --- Fetch by episode ID (optional season ID) ---
    elseif (isset($_GET['epid'])) {
        $episodeId = intval($_GET['epid']);
        $params = [$episodeId];
        $sql = "SELECT * FROM Episodes WHERE ";

        if (isset($_GET['seasonid'])) {
            $seasonId = intval($_GET['seasonid']);
            $sql .= "my_season_id = ? AND ";
            array_unshift($params, $seasonId);
        }

        $sql .= "episode_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetchAll();
    }

    // --- Fetch by seasonid and episode number ---
    elseif (isset($_GET['seasonid'], $_GET['episode'])) {
        $seasonId = intval($_GET['seasonid']);
        $episode = intval($_GET['episode']);

        $stmt = $pdo->prepare("SELECT * FROM Episodes WHERE my_season_id = ? AND epSort = ?");
        $stmt->execute([$seasonId, $episode]);
        $res = $stmt->fetchAll();
    }

    // --- If episode found, process image ---
    if (!empty($res)) {
        $res[0]['img'] = makeImgUrl("tmdb", $res[0]['img'], "mid");
    } else {
        echo json_encode(['error' => 'Episode not found']);
        exit;
    }

    // --- Handle links if requested ---
    if (isset($_GET['links']) && $_GET['links'] === "true") {
        $episodeId = $res[0]['episode_id'];

        if (isset($_GET['limit']) && $_GET['limit'] == 1) {
            $links = [];

            // PlayerX
            $stmt = $pdo->prepare("SELECT * FROM deadstream_playerx WHERE playerx != 'error' AND status = 1 AND is_episode = 1 AND cor_id = ? LIMIT 1");
            $stmt->execute([$episodeId]);
            $links['playerx'] = $stmt->fetch();

            if (!empty($links['playerx'])) {
                $links['playerx']['playerx'] = "https://boosterx.stream/v/{$links['playerx']['playerx']}/";
            }

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
