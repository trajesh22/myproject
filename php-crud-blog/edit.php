<?php include 'auth.php'; ?>
if (!isAdmin()) {
    die("Access denied. Admins only.");
}

<?php include 'config.php'; ?>

<?php
$error = "";
$title = "";
$content = "";

// Get post ID from URL
$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
    die("Invalid post ID.");
}

// Fetch current post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? '');
    $content = trim($_POST["content"] ?? '');
    $titleLength = strlen($title);

    // ✅ Server-side validation
    if ($title === '' || $content === '') {
        $error = "❌ Both title and content are required.";
    } elseif ($titleLength > 100) {
        $error = "❌ Title too long: $titleLength characters (max 100)";
    }

    // ✅ DEBUG: Write to file to inspect what's happening
    file_put_contents("debug-edit.txt", "Title: $title\nLength: $titleLength\nError: $error");

    // ✅ Only update if no error
    if ($error === "") {
        $update = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $update->execute([$title, $content, $id]);
        header("Location: index.php");
        exit();
    }
} else {
    // Pre-fill form if not submitted
    $title = $post['title'];
    $content = $post['content'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>
    <h2>Edit Post</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Title (max 100 characters):</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br><br>

        <label>Content:</label><br>
        <textarea name="content" required><?= htmlspecialchars($content) ?></textarea><br><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
