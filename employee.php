<?php
session_start();
require 'db.php';

// Strict Role Check
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'employee')) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    
    // SECURITY CHECK: Reject the entire submission if a script tag is found
    if (stripos($content, '<script') !== false) {
        $error = "Submission rejected: JavaScript (<script> tags) is strictly prohibited for security reasons.";
    } else {
        // Handle NEW blog submission
        if (isset($_POST['action']) && $_POST['action'] === 'create') {
            $title = trim($_POST['title']);
            
            $stmt = $pdo->prepare("INSERT INTO blogs (author_id, title, content, status) VALUES (?, ?, ?, 'under_review')");
            $stmt->execute([$_SESSION['user_id'], $title, $content]);
            $success = "New blog successfully submitted for admin review!";
        }
        // Handle REJECTED blog resubmission
        elseif (isset($_POST['action']) && $_POST['action'] === 'resubmit') {
            $blog_id = $_POST['blog_id'];
            
            $stmt = $pdo->prepare("UPDATE blogs SET content = ?, status = 'under_review', rejection_reason = NULL WHERE blog_id = ? AND author_id = ?");
            $stmt->execute([$content, $blog_id, $_SESSION['user_id']]);
            $success = "Blog successfully resubmitted for admin review!";
        }
    }
}

// Fetch all blogs by this logged-in user
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE author_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$my_blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow-sm p-3 mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Employee Dashboard</span>
            <a href="logout.php" class="btn btn-light btn-sm fw-bold text-primary">Logout</a>
        </div>
    </nav>
    
    <div class="container" style="max-width: 900px;">
        
        <?php if ($error): ?>
            <div class="alert alert-danger fw-bold shadow-sm"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success fw-bold shadow-sm"><?= $success ?></div>
        <?php endif; ?>

        <!-- NEW FORM: Create a Blog -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0 text-primary">Submit a New Blog</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="employee.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Blog Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content (HTML allowed)</label>
                        <textarea class="form-control" name="content" rows="6" required></textarea>
                        <small class="text-muted">Note: &lt;script&gt; tags will be automatically rejected.</small>
                    </div>
                    <button type="submit" name="action" value="create" class="btn btn-success fw-bold px-4">Submit for Review</button>
                </form>
            </div>
        </div>

        <h3 class="mb-4 text-primary border-bottom pb-2">My Submitted Blogs</h3>
        
        <?php if (count($my_blogs) == 0): ?>
            <p class="text-muted">You haven't submitted any blogs yet.</p>
        <?php else: ?>
            <?php foreach ($my_blogs as $blog): ?>
                
                <!-- If the blog is REJECTED, show the edit form -->
                <?php if ($blog['status'] === 'rejected'): ?>
                    <div class="card mb-4 border-danger shadow-sm border-2">
                        <div class="card-body p-4">
                            <h4 class="card-title text-danger fw-bold mb-3">⚠️ Status: Rejected - Needs Editing</h4>
                            <h5 class="text-dark">Title: <?= htmlspecialchars($blog['title']) ?></h5>
                            
                            <div class="alert alert-danger mt-3 mb-4">
                                <strong>Admin Feedback:</strong> <?= htmlspecialchars($blog['rejection_reason']) ?>
                            </div>
                            
                            <!-- Resubmission Form -->
                            <form method="POST" action="employee.php">
                                <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Edit your content:</label>
                                    <textarea class="form-control" name="content" rows="8" required><?= htmlspecialchars($blog['content']) ?></textarea>
                                </div>
                                <button type="submit" name="action" value="resubmit" class="btn btn-primary fw-bold px-4">Save & Resubmit</button>
                            </form>
                        </div>
                    </div>
                
                <!-- If the blog is NOT rejected, just show the status -->
                <?php else: ?>
                    <div class="card mb-3 shadow-sm border-start border-5 <?= $blog['status'] === 'approved' ? 'border-success' : 'border-warning' ?>">
                        <div class="card-body">
                            <h5 class="card-title text-dark"><?= htmlspecialchars($blog['title']) ?></h5>
                            <p class="text-muted mb-0">
                                Status: <strong><?= strtoupper($blog['status']) ?></strong> | 
                                Submitted on: <?= date("F j, Y", strtotime($blog['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>
</html>