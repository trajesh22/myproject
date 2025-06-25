<?php include 'auth.php'; ?>
<?php include 'config.php'; ?>

<?php
$error = "";
$title = "";
$content = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? '');
    $content = trim($_POST["content"] ?? '');

    // ✅ Server-side validation
    if (empty($title) || empty($content)) {
        $error = "❌ Title and content are required.";
    } elseif (strlen($title) >= 100) {
        $error = "❌ Title cannot exceed 100 characters.";
    } else {
        try {
            // ✅ Insert securely with prepared statement
            $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "❌ Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
</head>
<body>
    <h1>Create a New Post</h1>

    <!-- Show error if any -->
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Title (max 100 chars):</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" maxlength="100" required><br><br>

        <label>Content:</label><br>
        <textarea name="content" required><?= htmlspecialchars($content) ?></textarea><br><br>

        <button type="submit">Create</button>
    </form>
</body>
</html>
