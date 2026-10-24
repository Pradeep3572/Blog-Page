<?php
session_start();
require 'db.php';

// Strict Role Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 1. Handle Rejection with Reason (POST Request from Modal)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'reject') {
    $blog_id = $_POST['blog_id'];
    $reason = $_POST['rejection_reason'];

    
    // Update status to rejected and save the reason
    $stmt = $pdo->prepare("UPDATE blogs SET status = 'rejected', rejection_reason = ? WHERE blog_id = ?");
    $stmt->execute([$reason, $blog_id]);
    header("Location: admin.php");
    exit;
}

// 2. Handle User Approvals/Rejections (GET)
if (isset($_GET['user_action']) && isset($_GET['uid'])) {
    $u_action = $_GET['user_action'];
    $uid = $_GET['uid'];
    
    if ($u_action === 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
        $stmt->execute([$uid]);
    } elseif ($u_action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$uid]);
    }
    header("Location: admin.php");
    exit;
}

// 3. Handle Other Blog Status Updates (GET)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    
    if (in_array($action, ['approved', 'archived', 'deleted', 'under_review'])) {
        $stmt = $pdo->prepare("UPDATE blogs SET status = ? WHERE blog_id = ?");
        $stmt->execute([$action, $id]);
        header("Location: admin.php");
        exit;
    }
}

// Fetch Pending Users
$stmt = $pdo->query("SELECT * FROM users WHERE status = 'pending' ORDER BY id DESC");
$pending_users = $stmt->fetchAll();

// Fetch Blogs
$stmt = $pdo->query("SELECT b.*, u.username AS author_name FROM blogs b JOIN users u ON b.author_id = u.id WHERE b.status != 'deleted' ORDER BY b.created_at DESC");
$all_blogs = $stmt->fetchAll();

$pending_blogs = []; $approved_blogs = []; $archived_blogs = [];
foreach ($all_blogs as $blog) {
    if ($blog['status'] === 'under_review') $pending_blogs[] = $blog;
    if ($blog['status'] === 'approved') $approved_blogs[] = $blog;
    if ($blog['status'] === 'archived') $archived_blogs[] = $blog;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; color: #333; }
        header { background: #34495e; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        header a { color: white; text-decoration: none; background: #e74c3c; padding: 8px 15px; border-radius: 5px; }
        header a:hover { background: #c0392b; }
        .container { width: 90%; max-width: 1000px; margin: 30px auto; }
        .section-title { border-bottom: 2px solid #34495e; padding-bottom: 10px; margin-top: 40px; color: #2c3e50; }
        .blog-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 15px; border-left: 5px solid #bdc3c7;}
        .card-pending { border-left-color: #f39c12; }
        .card-approved { border-left-color: #27ae60; }
        .card-archived { border-left-color: #7f8c8d; background-color: #fdfdfd; }
        .card-user { border-left-color: #8e44ad; }
        .blog-actions { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
        .btn-custom { text-decoration: none; padding: 8px 15px; border-radius: 4px; color: white; margin-right: 10px; font-weight: bold; font-size: 0.9em; display: inline-block;}
        .btn-approve { background: #27ae60; }
        .btn-archive { background: #f39c12; }
        .btn-restore { background: #3498db; }
        .btn-delete { background: #e74c3c; cursor: pointer; border: none; }
        .btn-custom:hover { opacity: 0.8; color: white; }
        .empty-msg { color: #7f8c8d; font-style: italic; }
        .media-preview { margin: 15px 0; max-width: 100%; border-radius: 5px; max-height: 300px; }
    </style>
</head>
<body>
    <header>
        <h2>Admin Management Dashboard</h2>
        <a href="logout.php">Logout</a>
    </header>
    
    <div class="container">
        
        <!-- PENDING USERS -->
        <h3 class="section-title">New User Approvals</h3>
        <?php if (count($pending_users) == 0): ?>
            <p class="empty-msg">No new users waiting for approval.</p>
        <?php else: ?>
            <?php foreach ($pending_users as $user): ?>
                <div class="blog-card card-user">
                    <h4 style="margin:0 0 5px 0;">Username: <?= htmlspecialchars($user['username']) ?></h4>
                    <p style="font-size: 0.9em; color:#555; margin-bottom:10px;">
                        Requested Role: <strong><?= ucfirst(htmlspecialchars($user['role'])) ?></strong>
                    </p>
                    <div class="blog-actions">
                        <a href="?user_action=approve&uid=<?= $user['id'] ?>" class="btn-custom btn-approve">Approve Account</a>
                        <a href="?user_action=delete&uid=<?= $user['id'] ?>" class="btn-custom btn-delete" onclick="return confirm('Reject and delete this user?');">Reject & Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- SECTION 1: PENDING REVIEW -->
        <h3 class="section-title">1. Pending Blog Review</h3>
        <?php if (count($pending_blogs) == 0): ?>
            <p class="empty-msg">No blogs currently pending review.</p>
        <?php else: ?>
            <?php foreach ($pending_blogs as $blog): ?>
                <div class="blog-card card-pending">
                    <h3 style="margin-top:0; font-size: 1.6em;"><?= htmlspecialchars($blog['title']) ?></h3>
                    <p style="font-size: 0.9em; color:#555; margin-bottom:15px;">
                        Author: <strong><?= htmlspecialchars($blog['author_name']) ?></strong> (User ID: <?= $blog['author_id'] ?>) <br>
                        Blog ID: <?= $blog['blog_id'] ?> | Submitted: <?= $blog['created_at'] ?>
                    </p>
                   <p style="line-height: 1.6;"><?= $blog['content'] ?></p>
                    
                    <?php if ($blog['image_path']): ?>
                        <div><strong>Attached Image:</strong><br><img src="<?= $blog['image_path'] ?>" class="media-preview"></div>
                    <?php endif; ?>
                    
                    <?php if ($blog['video_path']): ?>
                        <div><strong>Attached Video:</strong><br>
                        <video class="media-preview" controls>
                          <source src="<?= $blog['video_path'] ?>" type="video/mp4">
                        </video></div>
                    <?php endif; ?>

                    <div class="blog-actions">
                        <a href="?action=approved&id=<?= $blog['blog_id'] ?>" class="btn-custom btn-approve">Approve Post</a>
                        <!-- CHANGED: Now opens the dialog box instead of immediately deleting -->
                        <button type="button" class="btn-custom btn-delete" onclick="openRejectModal('<?= $blog['blog_id'] ?>')">Reject with Reason</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- SECTION 2: APPROVED (LIVE) -->
        <h3 class="section-title">2. Live / Approved Blogs</h3>
        <?php if (count($approved_blogs) == 0): ?>
            <p class="empty-msg">No blogs are currently live.</p>
        <?php else: ?>
            <?php foreach ($approved_blogs as $blog): ?>
                <div class="blog-card card-approved">
                    <h4 style="margin:0 0 5px 0;"><?= htmlspecialchars($blog['title']) ?></h4>
                    <p style="font-size: 0.9em; color:#555; margin-bottom:10px;">
                        Author: <strong><?= htmlspecialchars($blog['author_name']) ?></strong> (User ID: <?= $blog['author_id'] ?>) <br>
                        Blog ID: <?= $blog['blog_id'] ?>
                    </p>
                    <div class="blog-actions">
                        <a href="?action=archived&id=<?= $blog['blog_id'] ?>" class="btn-custom btn-archive">Move to Archive</a>
                        <a href="?action=under_review&id=<?= $blog['blog_id'] ?>" class="btn-custom btn-restore">Send Back to Pending</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- SECTION 3: ARCHIVED -->
        <h3 class="section-title">3. Archived Blogs</h3>
        <?php if (count($archived_blogs) == 0): ?>
            <p class="empty-msg">Archive is empty.</p>
        <?php else: ?>
            <?php foreach ($archived_blogs as $blog): ?>
                <div class="blog-card card-archived">
                    <h4 style="margin:0 0 5px 0;"><?= htmlspecialchars($blog['title']) ?></h4>
                    <p style="font-size: 0.9em; color:#555; margin-bottom:10px;">
                        Author: <strong><?= htmlspecialchars($blog['author_name']) ?></strong> (User ID: <?= $blog['author_id'] ?>) <br>
                        Blog ID: <?= $blog['blog_id'] ?>
                    </p>
                    <div class="blog-actions">
                        <a href="?action=approved&id=<?= $blog['blog_id'] ?>" class="btn-custom btn-restore">Restore & Publish</a>
                        <a href="?action=deleted&id=<?= $blog['blog_id'] ?>" class="btn-custom btn-delete" onclick="return confirm('Permanently delete this archived blog?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- REJECTION MODAL (Dialog Box) -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" action="admin.php">
            <div class="modal-header">
              <h5 class="modal-title">Reject Blog</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
              <!-- Hidden input to pass the ID to PHP -->
              <input type="hidden" name="blog_id" id="modal_blog_id">
              
              <!-- GUARANTEE the action is sent to PHP -->
              <input type="hidden" name="action" value="reject">
              
              <div class="mb-3">
                <label class="form-label text-dark">Reason for rejection (sent to employee):</label>
                <textarea class="form-control" name="rejection_reason" required rows="4" placeholder="Explain what needs to be fixed..."></textarea>
              </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <!-- Removed the name/value from here to prevent conflicts with the hidden input -->
              <button type="submit" class="btn btn-danger">Confirm Rejection</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- JavaScript to trigger the Modal safely -->
    <script>
    function openRejectModal(blogId) {
        // Pass the ID to the hidden input
        document.getElementById('modal_blog_id').value = blogId;
        
        // Use getOrCreateInstance to prevent Bootstrap freezing/overlapping bugs
        var rejectModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('rejectModal'));
        rejectModal.show();
    }
    </script>
</body>
</html>