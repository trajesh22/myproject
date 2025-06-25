<?php
require 'config.php';
$id = 1; // change to an existing post ID

$longTitle = str_repeat("A", 150);
$content = "Manual test to check server-side validation bypass.";

$stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
$stmt->execute([$longTitle, $content, $id]);

echo "Updated post #$id with title length: " . strlen($longTitle);
?>
