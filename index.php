<?php
session_start();
require 'db.php';

// Strict Role Check
// Strict Role Check: Allow both 'user' and 'employee' roles
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'employee')) {
    header("Location: login.php");
    exit;
}

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT b.*, u.username AS author_name FROM blogs b JOIN users u ON b.author_id = u.id WHERE b.status = 'approved' AND (b.title LIKE ? OR b.content LIKE ?) ORDER BY b.created_at DESC");
    $stmt->execute(['%' . $search_query . '%', '%' . $search_query . '%']);
} else {
    $stmt = $pdo->query("SELECT b.*, u.username AS author_name FROM blogs b JOIN users u ON b.author_id = u.id WHERE b.status = 'approved' ORDER BY b.created_at DESC");
}
$blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Reader</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-family: 'Georgia', serif; font-size: 1.8rem; font-weight: bold; }
        .search-container { background: white; padding: 40px 0; border-bottom: 1px solid #e9ecef; margin-bottom: 40px; }
        .article-card { border: none; border-radius: 12px; }
        .meta-info { color: #6c757d; font-size: 0.9rem; border-bottom: 2px solid #f8f9fa; padding-bottom: 15px; margin-bottom: 20px; }
        .media-preview { max-width: 100%; border-radius: 8px; margin: 20px auto; display: block; }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">The Daily Blog</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-light btn-sm fw-bold text-primary">Logout</a>
            </div>
        </div>
    </nav>
    
    <!-- Search Bar -->
    <div class="search-container shadow-sm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form method="GET" class="d-flex">
                        <input class="form-control form-control-lg me-2 rounded-pill px-4" type="search" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search articles by keywords...">
                        <button class="btn btn-primary btn-lg rounded-pill px-4" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Feed -->
    <div class="container" style="max-width: 900px;">
        
        <?php if (count($blogs) == 0): ?>
            <div class="alert alert-secondary text-center fs-5 mt-5 p-5 rounded-4 shadow-sm">
                No articles found. Try searching for something else!
            </div>
        <?php endif; ?>

        <?php foreach ($blogs as $blog): ?>
            <div class="card article-card shadow-sm mb-5">
                <div class="card-body p-5">
                    <h2 class="card-title fw-bold text-dark mb-3"><?= htmlspecialchars($blog['title']) ?></h2>
                    
                    <div class="meta-info">
                        By <span class="fw-bold text-primary"><?= htmlspecialchars($blog['author_name']) ?></span> 
                        &nbsp;|&nbsp; Published on <?= date("F j, Y", strtotime($blog['created_at'])) ?> 
                    </div>
                    
                    <?php if ($blog['image_path']): ?>
                        <img src="<?= $blog['image_path'] ?>" alt="Blog Image" class="media-preview shadow-sm">
                    <?php endif; ?>
                    
                    <div class="article-content mt-4" style="overflow-x: auto;">
                        <?php 
                            /* 
                             * OUTPUT RAW HTML: 
                             * Since we blocked scripts in employee.php, we can output the raw content here.
                             * This allows employees to write <style> tags and full HTML layouts.
                             */
                            echo $blog['content']; 
                        ?>
                    </div>
                    
                    <?php if ($blog['video_path']): ?>
                        <div class="mt-4 text-center">
                            <video controls class="media-preview shadow-sm" style="max-height: 500px;">
                                <source src="<?= $blog['video_path'] ?>" type="video/mp4">
                            </video>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
    </div>
</body>
</html>