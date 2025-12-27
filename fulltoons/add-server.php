<?php

$db_host = 'localhost';
$db_name = 'fulltoon_Anime';
$db_user = 'fulltoon_anime';
$db_pass = '6@7A8a9a';

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("INSERT INTO Server_info (Name, DName, Domain, faIcon, btnType, Color) VALUES (:name, :dname, :domain, :icon, :type, :color)");

    // Bind parameters from form data
    $name = $_POST['name'];
    $dname = $_POST['dname'];
    $domain = $_POST['domain'];
    $icon = $_POST['icon'];
    $type = $_POST['type'];
    $color = $_POST['color'];

    // Bind parameters to statement placeholders
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':dname', $dname);
    $stmt->bindParam(':domain', $domain);
    $stmt->bindParam(':icon', $icon);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':color', $color);

    // Execute the statement
    try {
        $stmt->execute();
        echo "Data inserted successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
