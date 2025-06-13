<?php include 'auth.php'; ?>
<?php include 'config.php'; ?>
<p>Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a></p>

<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
</head>
<body>
    <h2>Blog Posts</h2>
    <a href="create.php">+ Add New Post</a>
    <hr>
    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()) {
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        echo "<a href='edit.php?id=" . $row['id'] . "'>Edit</a> | ";
        echo "<a href='delete.php?id=" . $row['id'] . "'>Delete</a><hr>";
    }
    ?>
</body>
</html>