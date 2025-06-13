<?php include 'auth.php'; ?>
<?php
// Show all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
$mysqli = new mysqli("localhost", "root", "admin123", "blog", 3307);

// Check connection
if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"] ?? '';
    $content = $_POST["content"] ?? '';

    // Validate inputs (optional)
    if (empty($title) || empty($content)) {
        echo "Both title and content are required.";
    } else {
        // Use prepared statement to insert
        $stmt = $mysqli->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
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
    <form method="POST" action="">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Content:</label><br>
        <textarea name="content" required></textarea><br><br>

        <button type="submit">Create</button>
    </form>
</body>
</html>
