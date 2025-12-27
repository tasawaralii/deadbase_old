<?php


// Establish database connection
$pdo = new PDO("mysql:host=localhost;dbname=u954292975_Anime", "u954292975_pxanime", "1A3a4b5@");
// Check if user is already logged in
if (isset($_COOKIE['login'])) {
    header("Location: add");
    exit; // Make sure to exit after redirecting
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Retrieve username and password from form
    $u = $_POST['username'];
    $p = $_POST['password'];
    // Prepare SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM User WHERE username = :username AND password = :password");
    // Bind parameters
    $stmt->bindParam(':username', $u);
    $stmt->bindParam(':password', $p);
    // Execute the query
    if ($stmt->execute()) {
        // Fetch the result
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            // User authenticated, set cookie and redirect
            setcookie("login", $u, time() + (86400 * 30), "/");
            header("Location: add");
            exit; // Make sure to exit after redirecting
        } else {
            echo "Banda BAn JA";
        }
    } else {
        // Handle query execution error
        echo "Error executing query";
    }
}
?>

<form method="post">
    <label>Usename</label>
    <input type="text" name="username">
    <br>
    <label>Password</label>
    <input type="password" name="password">
    <input type="submit" value="Login">
</form>
