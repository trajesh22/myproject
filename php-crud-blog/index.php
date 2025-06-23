<?php include 'auth.php'; ?>
<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
    <!-- ✅ Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">

    <!-- 🔐 Welcome & Logout -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><strong>Welcome, <?php echo $_SESSION['username']; ?></strong></div>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

    <h2 class="mb-3">Blog Posts</h2>

    <!-- ➕ Add New Post -->
    <a href="create.php" class="btn btn-success mb-3">+ Add New Post</a>

    <!-- 🔍 Search Form -->
    <form method="GET" action="" class="input-group mb-4">
        <input 
            type="text" 
            name="search" 5
            class="form-control" 
            placeholder="Search by title or content..." 
            value="<?php echo htmlspecialchars($_GET['search'] ?? '') ?>"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php
    // 🔢 Pagination variables
    $postsPerPage = 5;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $postsPerPage;

    // 🔎 Search logic
    $search = $_GET['search'] ?? '';
    $sql = "SELECT * FROM posts";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $sql .= " WHERE title LIKE ? OR content LIKE ?";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types = 'ss';
    }

    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $postsPerPage;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // 📄 Display Posts
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card mb-3'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>";
            echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
            echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning me-2'>Edit</a>";
            echo "<a href='delete.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger'>Delete</a>";
            echo "</div></div>";
        }
    } else {
        echo "<div class='alert alert-info'>No posts found.</div>";
    }
    $stmt->close();

    // 🔁 Pagination Count
    $countSql = "SELECT COUNT(*) AS total FROM posts";
    $countParams = [];
    $countTypes = '';

    if (!empty($search)) {
        $countSql .= " WHERE title LIKE ? OR content LIKE ?";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
        $countTypes = 'ss';
    }

    $countStmt = $conn->prepare($countSql);
    if (!empty($countParams)) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalPosts = $countResult->fetch_assoc()['total'];
    $countStmt->close();

    $totalPages = ceil($totalPosts / $postsPerPage);

    // 📌 Show Pagination Links
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
</div> <!-- End Container -->

<!-- ✅ Optional Bootstrap JS (for advanced features like modals/dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>