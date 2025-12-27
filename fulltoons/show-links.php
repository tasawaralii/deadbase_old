<?php

require('functions.php');
require_once('db.php');

$deadstream = "https://animedisk.me/";
$animeworld = "https://animeworld-india.me/";
$deadtoons = "https://deadtoons.org/";
$archive = "https://archive.deadtoons.org";
$animeDisk = "https://animedisk.me";

$anime_id = $_GET['animeId'];
$animeType = $_GET['type'];
$season = $_GET['season'] ?? 0;
$hindi_only = $_GET['hinOnly'] ?? 0;

$make = new MakeResponse();

if ($animeType == "tv") {

    if ($season == "all") {
        $make->AllSeasons($pdo, $anime_id);
    } else {
        $make->SingleSeason($pdo, $anime_id, $season, $hindi_only);
    }
} else {

    $make->Movie($pdo, $anime_id);
    
}


class MakeResponse
{
    public function SingleSeason($pdo, $anime_id, $season, $hindi_only)
    {

        global $archive;
        $previousEpisodeNum = null;
        $skippedEpisodes = [];

        $sql = $pdo->query("
            
            SELECT DISTINCT 
                Animes.slug,Animes.anime_name ,
                my_seasons.my_season_num,my_seasons.my_season_id, 
                Episodes.episode_name, Episodes.epSort, Episodes.part, Episodes.img, Episodes.note 
            FROM Episodes 
                Join my_seasons on my_seasons.my_season_id = Episodes.my_season_id
                JOIN Animes on Animes.anime_id = my_seasons.anime_id 
                JOIN EpisodeLinks on Episodes.episode_id = EpisodeLinks.episode_id 
            Where 
                my_seasons.my_season_id = (SELECT my_season_id FROM `my_seasons` WHERE `anime_id` = $anime_id AND `my_season_num` = $season) 
            
            ORDER BY `Episodes`.`epSort` ASC
            ");
        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

        if (!$res) {
            echo "Season Not Found";
            exit;
        }

        echo '<center><h4><strong style="color:red;">[Season ' . $res[0]['my_season_num'] . ']</strong></h4></center><hr>';
        echo img($res[0]['my_season_id'], $pdo);

        foreach ($res as $row) {

            $currentEpisodeNum = $row['epSort'];
            if ($previousEpisodeNum !== null && $currentEpisodeNum - $previousEpisodeNum > 1) {
                for ($i = $previousEpisodeNum + 1; $i < $currentEpisodeNum; $i++) {
                    $skippedEpisodes[] = $i;
                }
            }


            $previousEpisodeNum = $currentEpisodeNum;


            $name = str_replace('\\', '', $row['episode_name']);
            $name = preg_replace("/Episode \d+: /", '', $name);


            echo "<p><center><strong>Episode {$row['epSort']}{$row['part']}: {$name} <b style='color:red'>" . (($row['note'] != '') ? "(" . $row['note'] . ")" : '') . "</b></strong></center></p>";
            echo '<center><strong>';
            echo '<a rel="nofollow" href="' . $archive . '/episode/' . $row['slug'] . '/' . $row['my_season_num'] . 'x' . $row['epSort'] . '">Download</a>';
            echo '</strong></center>';
            echo "<hr>";

        }


        if (!empty($skippedEpisodes)) {
            echo "<center>";
            if (count($skippedEpisodes) == 1) {
                echo "Note : <b>Episode " . $skippedEpisodes[0] . " is not available and will be added when available</b>";
            } else {
                echo "Note: <b>Following are skipped episodes and will be added when available: <font color='red'>" . implode(',', $skippedEpisodes) . "</font></b>";
            }
            echo "</center><hr>";
        }


        $psql = $pdo->query("
                SELECT 
                    CONCAT(Packs.startEp, '-', Packs.endEp) AS startEndEp, 
                    Packs.*, 
                    li.uid,
                    Animes.anime_name, 
                    my_seasons.my_season_num 
                FROM Packs 
                JOIN Links_info li ON li.Id = Packs.drive_id
                JOIN my_seasons ON Packs.my_season_id = my_seasons.my_season_id 
                JOIN Animes ON my_seasons.anime_id = Animes.anime_id 
                WHERE my_seasons.my_season_id = (
                    SELECT my_season_id 
                    FROM my_seasons 
                    WHERE anime_id = $anime_id AND my_season_num = $season
                ) 
                AND honly = $hindi_only
                ORDER BY Packs.startEp ASC, Packs.endEp DESC, Packs.size ASC
            ");

        $pres = $psql->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);

        if (count($pres)) {
            echo "<h3 style='color: #ff6600;'><center>[Pack Links]</center></h3><hr>";
            foreach ($pres as $startEndEp => $packs) {
                list($startEp, $endEp) = explode("-", $startEndEp);
                $startEp = str_pad($startEp, 2, '0', STR_PAD_LEFT);
                $endEp = str_pad($endEp, 2, '0', STR_PAD_LEFT);
                echo "<strong><center> EP{$startEp} - EP{$endEp}</center></strong><hr>";
                foreach ($packs as $pack_row) {
                    $deaddrive_packLink = "https://deaddrive.shop/file/{$pack_row['uid']}";
                    $url = "https://archive.deadtoons.one/redirect?url=";
                    $url = $url . "zylith" . AES("encrypt", $deaddrive_packLink);
                    echo "<strong><center><a rel='nofollow' href={$url} target='_blank'>{$pack_row['anime_name']} S" . str_pad($pack_row['my_season_num'], 2, '0', STR_PAD_LEFT) . " {$pack_row['quality']} - [" . formatSize($pack_row['size']) . "]</a></center></strong><hr>";
                }
            }


        }

        $otherSeasons = $pdo->query("SELECT * FROM my_seasons WHERE anime_id = $anime_id")->fetchAll(PDO::FETCH_ASSOC);
        if (count($otherSeasons) > 1) {
            echo "<center>";
            echo "<h3 style='color:#993300'>Other Seasons</h3><hr>";
            foreach ($otherSeasons as $os) {
                if ($os['my_season_num'] == $season) {
                    echo "<h4>Season " . $os['my_season_num'] . "</h4>";
                } else {
                    $ss = "/?s=" . $res[0]['anime_name'] . " season " . $os['my_season_num'];
                    $ss = str_replace(" ", "+", $ss);

                    echo "<h4><a href='$ss'>Season " . $os['my_season_num'] . "</a></h4>";

                }
            }
            echo "</center>";
        }

    }

    public function AllSeasons($pdo, $anime_id)
    {

        global $archive;

        $hi = $_GET['hinOnly'];

        $allEpisodes = "SELECT ms.my_season_num, ms.my_season_name, e.episode_name, e.epSort, a.slug FROM Episodes e JOIN my_seasons ms ON ms.my_season_id = e.my_season_id JOIN Animes a ON a.anime_id = ms.anime_id WHERE a.anime_id = $anime_id AND
                        EXISTS (SELECT 1 FROM EpisodeLinks el WHERE el.episode_id = e.episode_id AND el.Hindi_Only = $hi AND EXISTS (SELECT 1 FROM Links_info li WHERE li.Id = el.drive_id));))";

        // $allSeason = "SELECT DISTINCT 
        // my_seasons.my_season_num,my_seasons.my_season_name,
        // Links_info.uid,
        // Episodes.episode_name,Episodes.epSort,
        // Animes.slug FROM my_seasons 
        // JOIN Animes ON Animes.anime_id = my_seasons.anime_id 
        // JOIN Episodes ON Episodes.my_season_id = my_seasons.my_season_id 
        // JOIN EpisodeLinks ON EpisodeLinks.episode_id = Episodes.episode_id
        // JOIN Links_info ON EpisodeLinks.drive_id = Links_info.Id
        // WHERE  my_seasons.anime_id = $anime_id and EpisodeLinks.Hindi_Only = $hi";
        $allSRes = $pdo->query($allEpisodes)->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        foreach ($allSRes as $n => $s) {
            echo '<center><strong>Season ' . $n . ' (' . $s[0]['my_season_name'] . ')' . '</strong></center><hr>';
            foreach ($s as $e) {
                // print_r($e);
                // if($hi == 1) {
                //     echo '<center><strong>'.'<a rel="nofollow" href="'.makeRedirect($e['uid']).'">'.'Episode '.$e['epSort'].' - '.$e['episode_name'].'</a></strong></center><hr>';
                // } else {
                echo '<center><strong>' . '<a rel="nofollow" href="' . $archive . '/episode/' . $e['slug'] . '/' . $n . 'x' . $e['epSort'] . '">' . 'Episode ' . $e['epSort'] . ' - ' . $e['episode_name'] . '</a></strong></center><hr>';

                // }
            }
        }

        $allpack = $pdo->query("SELECT 
        CONCAT('Season ', my_seasons.my_season_num, ' (', my_seasons.my_season_name, ')') AS name,
        Packs.quality,Packs.size,Packs.startEp,Packs.endEp,
        Links_info.uid 
        FROM Packs 
        JOIN my_seasons ON my_seasons.my_season_id = Packs.my_season_id 
        JOIN Animes ON Animes.anime_id = my_seasons.anime_id 
        JOIN Links_info ON Links_info.Id = Packs.drive_id 
        WHERE Animes.anime_id = $anime_id and Packs.honly = $hi ORDER BY my_seasons.my_season_num ASC, Packs.startEp ASC")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        if ($allpack) {
            echo "<center><h2>Pack Links</h2></center><hr>";

            foreach ($allpack as $name => $links) {
                echo "<center><strong>";
                echo $name . "<hr>";

                foreach ($links as $l) {
                    echo "Ep " . str_pad($l['startEp'], 3, "0", STR_PAD_LEFT) . " to " . "Ep " . str_pad($l['endEp'], 3, "0", STR_PAD_LEFT) . "	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a rel='nofollow' href='" . makeRedirect($l['uid']) . "'>" . $l['quality'] . " - " . "[" . formatSize($l['size']) . "]" . "</a><br>";
                }
                echo "<hr>";
            }
            echo "</center></strong>";
        }

    }

    public function Movie($pdo, $anime_id)
    {
        global $archive;
        global $animeDisk;

        $m = $pdo->query("SELECT * FROM `movieLinks` JOIN Animes on Animes.anime_id = movieLinks.anime_id JOIN Links_info ON Links_info.Id = movieLinks.movie_drive_id WHERE Animes.anime_id = $anime_id ORDER BY `movieLinks`.`size` ASC");
        $l = $m->fetchAll(PDO::FETCH_ASSOC);

        if ($l) {

            echo "<h4><center>Movie Links</center></h4><hr>";

            echo "<center><strong>Watch Online at <a rel='nofollow' href='$animeDisk'>AnimeDisk</a></strong></center><hr>";


            foreach ($l as $movie) {

                $url = $archive . "/movie/" . $l[0]['slug'];
                echo "<strong><center>
<a rel='nofollow' href='{$url}' target='_blank'>{$movie['quality']} - [" . formatSize($movie['size']) . "]</a>
</center></strong><hr>";

            }

        } else {
            echo "<p style='color:#993300'><center>Links Are being processed....</center></p>";
        }

    }
}

function img($msid, $pdo)
{
    $res = $pdo->query("SELECT img 
FROM Episodes 
JOIN EpisodeLinks ON EpisodeLinks.episode_id = Episodes.episode_id 
JOIN Links_info ON Links_info.Id = EpisodeLinks.drive_id 
WHERE my_season_id = $msid AND img != '' 
GROUP BY img 
ORDER BY RAND() 
LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
    $html = '';
    if ($res) {
        $html .= '<center><h4><strong style="color:red;">ScreenShots</strong></h4></center>';
        foreach ($res as $i) {
            $html .= '<img style="display:block;margin:auto;" src="https://image.tmdb.org/t/p/w780' . $i['img'] . '">';
        }
    }
    return $html . "<hr>";
}


function makeRedirect($uid)
{
    return "https://new2.deaddrive.shop/file/$uid";
}

function AES($action, $string)
{
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'deadtoons';
    $secret_iv = 'fake';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}


?>