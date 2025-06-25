<?php include 'auth.php'; ?>
<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">

    <!-- 🔐 Welcome & Logout -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <strong>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <span class="badge bg-info text-dark ms-2"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
        </div>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

    <h2 class="mb-3">Blog Posts</h2>

    <!-- ➕ Add New Post -->
    <a href="create.php" class="btn btn-success mb-3">+ Add New Post</a>

    <!-- 🔍 Search Form -->
    <form method="GET" action="" class="input-group mb-4">
        <input 
            type="text" 
            name="search"
            class="form-control" 
            placeholder="Search by title or content..." 
            value="<?php echo htmlspecialchars($_GET['search'] ?? '') ?>"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php
    // 🔢 Pagination
    $postsPerPage = 5;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $postsPerPage;

    // 🔍 Search logic
    $search = $_GET['search'] ?? '';
    $sql = "SELECT * FROM posts";
    $params = [];

    if (!empty($search)) {
        $sql .= " WHERE title LIKE :search OR content LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $postsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();

    // 📄 Display posts
    if ($posts) {
        foreach ($posts as $row) {
            echo "<div class='card mb-3'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>";
            echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";

            // 🔐 Only admins see Edit/Delete
            if (isAdmin()) {
                echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning me-2'>Edit</a>";
                echo "<a href='delete.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger'>Delete</a>";
            }

            echo "</div></div>";
        }
    } else {
        echo "<div class='alert alert-info'>No posts found.</div>";
    }

    // 🔁 Pagination
    $countSql = "SELECT COUNT(*) AS total FROM posts";
    if (!empty($search)) {
        $countSql .= " WHERE title LIKE :search OR content LIKE :search";
    }

    $countStmt = $pdo->prepare($countSql);
    if (!empty($search)) {
        $countStmt->bindValue(':search', $params[':search'], PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalPosts = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalPosts / $postsPerPage);

    // 📌 Pagination links
    if ($totalPages > 1) {
        echo "<nav><ul class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $link = "?page=$i";
            if (!empty($search)) {
                $link .= "&search=" . urlencode($search);
            }
            $active = ($i == $page) ? 'active' : '';
            echo "<li class='page-item $active'><a class='page-link' href='$link'>$i</a></li>";
        }
        echo "</ul></nav>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
