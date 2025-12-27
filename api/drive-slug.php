<?php


    if(isset($_GET['drive']) && isset($_GET['slug']) && isset($_GET['size'])) {
        require("../db.php");
        
        echo "Drive : ";
        
        echo $drive = $_GET['drive'];
        
        echo "<br>" . "Uid : ";
        
        echo $slug = $_GET['slug'];
        
        echo "<br>" . "Size : ";
        
        echo $size = intval($_GET['size']);
        
        echo "<br>";
            
        echo $sql = "INSERT INTO Links_info (Id,size,uid) VALUES ('$drive',$size,'$slug') ON DUPLICATE KEY UPDATE uid = VALUES(uid)";
        
        $pdo->query($sql);
        
        echo "<br>" . "done";
    } else {
        echo "Missing";
    }