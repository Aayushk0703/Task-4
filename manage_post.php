<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

$deleted = isset($_GET['deleted']) ? (int)$_GET['deleted'] : 0;
$updated = isset($_GET['updated']) ? (int)$_GET['updated'] : 0;

// Fetch all posts
$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .table th {
            background-color: #dee2e6;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if ($deleted === 1): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="deleteAlert">
                ✅ Post deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($updated === 1): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert" id="updateAlert">
                ✏️ Post updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg">
            <div class="card-header">
                <h3>📝 Manage Posts</h3>
            </div>
            <div class="card-body">
                <p class="mb-3">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
                <span class="badge bg-dark mb-4">Role: Admin</span>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td><?= htmlspecialchars($post['title']) ?></td>
                            <td><?= htmlspecialchars($post['username']) ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_post.php?id=<?= $post['id'] ?>&redirect_to=manage_post.php" class="btn btn-sm btn-outline-primary"> Edit</a>

                                    <a href="delete_post.php?id=<?= $post['id'] ?>&redirect_to=manage_post.php" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                    <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="admin_panel.php" class="btn btn-outline-dark mt-3">🔙 Back to Admin Panel</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS for alert dismiss -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-dismiss alerts after 3 seconds -->
    <script>
        setTimeout(() => {
            const deleteAlert = document.getElementById('deleteAlert');
            if (deleteAlert) {
                deleteAlert.classList.remove('show');
                deleteAlert.classList.add('fade');
            }
            const updateAlert = document.getElementById('updateAlert');
            if (updateAlert) {
                updateAlert.classList.remove('show');
                updateAlert.classList.add('fade');
            }
        }, 3000);
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
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
        .navbar { border-radius: 8px; }
        .notification-box { min-height: 60px; }
        .card { border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card-footer .btn { border-radius: 6px; }
        .pagination .page-link { border-radius: 6px; }
        .classy-logout {
            background: linear-gradient(to right, rgb(88, 73, 203), rgb(241, 151, 124));
            color: white; border: none; border-radius: 8px;
            padding: 8px 16px; font-weight: 500;
            transition: background 0.3s ease;
        }
        .classy-logout:hover {
            background: linear-gradient(to right, rgb(88, 73, 203), rgb(241, 151, 124));
        }
        .classy-search {
            background: linear-gradient(to right, rgb(196, 137, 185), rgb(241, 151, 124));
            color: white; border: none; border-radius: 8px;
            padding: 8px 16px; font-weight: 500;
            transition: background 0.3s ease;
        }
    </style>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => {
        const alert = document.getElementById('updateAlert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
        }
    }, 3000);
</script>
