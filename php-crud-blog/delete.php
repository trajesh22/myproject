<?php include 'auth.php'; ?>
if (!isAdmin()) {
    die("Access denied. Admins only.");
}

<?php include 'config.php'; ?>

<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Use prepared statement to safely delete the post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: index.php");
    exit();
} else {
    echo "❌ Invalid request. No post ID provided.";
}
?>
