<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

function isAdmin() {
    return isset($_SESSION["role"]) && $_SESSION["role"] === 'admin';
}
?>
