<?php
$host = "localhost";
$port = 3307; // Use your port number here
$username = "root";
$password = "admin123"; // Use your actual password
$database = "blog";

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>