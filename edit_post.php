<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$error = "";

// Fetch post for editing
if ($post_id) {
    try {
        if ($role === 'admin') {
            $sql = "SELECT * FROM posts WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $post_id]);
        } else {
            $sql = "SELECT * FROM posts WHERE id = :id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $post_id, 'user_id' => $user_id]);
        }

        $post = $stmt->fetch();
        if (!$post) {
            die("❌ Post not found or unauthorized access.");
        }
    } catch (PDOException $e) {
        die("❌ Error fetching post: " . $e->getMessage());
    }
} else {
    die("❌ Invalid post ID.");
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        try {
            $sql = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'id' => $post_id
            ]);

            // Redirect with success flag based on role
            // Determine where to redirect after update
$redirect_to = $_GET['redirect_to'] ?? 'index.php';
header("Location: {$redirect_to}?updated=1");
exit;

        } catch (PDOException $e) {
            $error = "❌ Error updating post: " . $e->getMessage();
        }
    } else {
        $error = "❌ All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .container {
            max-width: 700px;
            margin-top: 40px;
            margin-bottom: 60px;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notification-box {
            min-height: 60px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">✏️ Edit Post</h2>

    <div class="notification-box" id="messageBox">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger fw-bold" id="message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Content</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="submit" class="btn btn-primary">💾 Update Post</button>
            <a href="<?= ($role === 'admin') ? 'manage_post.php' : 'index.php' ?>" class="btn btn-outline-dark">🏠 Go to Dashboard</a>
        </div>
    </form>
</div>

<script>
  setTimeout(function() {
    const msg = document.getElementById("message");
    if (msg) {
      msg.style.transition = "opacity 0.5s ease";
      msg.style.opacity = "0";
      setTimeout(() => msg.remove(), 500);
    }
  }, 2000);
</script>
</body>
</html>
