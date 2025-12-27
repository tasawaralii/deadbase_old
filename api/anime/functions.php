<?php

function AES($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'deadtoons';
    $secret_iv = 'fake';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}


function fetch($url) {
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);

    return $response;
}


function makeImgUrl($mode,$slug,$quality,$poster = false) {
    
    if($slug == "empty")
        return "";
        
    // tmdb     : "https://image.tmdb.org/t/p/w342/qGdvJCuZLL3XTlaVcokjmjOYwVT.jpg"
    // deadbase : "https://deadbase.xyz/content/2024/06/code-white-e1718909020550-640x360.png"


    $tmdbDomain = "https://image.tmdb.org/t/p/";
    
    $qualities = [
                    'tmdb' => [
                            'poster' =>
                                [
                                    'low' => 'w154',
                                    'mid' => 'w342',
                                    'high' => 'w780'
                                ],
                            'backdrop' =>
                                [
                                    'low' => 'w300',
                                    'mid' => 'w780',
                                    'high' => 'w1280'
                                ]
                            ]
                ];
    
    if($mode == "tmdb") {
        
        if($poster)
            return $tmdbDomain . $qualities['tmdb']['poster']["$quality"] . $slug;
        return $tmdbDomain . $qualities['tmdb']['backdrop']["$quality"] . $slug;
        
    }
    
    if($mode == "deadbase") {
        
        if($quality == "low" || $quality == "mid")
            return "https://deadbase.xyz/content/" . pathinfo($slug, PATHINFO_DIRNAME) . pathinfo($slug, PATHINFO_FILENAME) . "-640x360" . pathinfo($slug, PATHINFO_EXTENSION);
        return "https://deadbase.xyz/content".$slug;
        
    }
    
    if($mode == "other")
        return $slug;
    
}

function makeImgUrlAnime(&$anime,$quality) {
    
    if($anime['backdrop_source'] == 1)
        $anime['backdrop_img'] = makeImgUrl("tmdb",$anime['backdrop_img'],$quality);
    elseif($anime['backdrop_source'] == 2)
        $anime['backdrop_img'] = makeImgUrl("deadbase",$anime['backdrop_img'],$quality);
    else
        $anime['backdrop_img'] = makeImgUrl("other",$anime['backdrop_img'],$quality);
        
        
        
    if($anime['poster_source'] == 1)
        $anime['poster_img'] = makeImgUrl("tmdb",$anime['poster_img'],$quality,true);
    elseif($anime['poster_source'] == 2)
        $anime['poster_img'] = makeImgUrl("deadbase",$anime['poster_img'],$quality);
    else
        $anime['poster_img'] = makeImgUrl("other",$anime['poster_img'],$quality);
    
}