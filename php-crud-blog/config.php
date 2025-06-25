<?php
$host = 'localhost';
$port = 3307; // Your custom port
$db = 'blog';
$user = 'root';
$pass = 'admin123'; // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
