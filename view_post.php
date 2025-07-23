<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    die("❌ Invalid post ID.");
}

$sql = "SELECT posts.*, users.username FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("❌ Post not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1000px;
            margin-top: 50px;
            margin-bottom: 60px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .meta-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            height: 100%;
        }
        .content-section h4 {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .content-section p {
            white-space: pre-wrap;
        }
        .back-btn {
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row g-4">
        <!-- Left Column: Meta Info -->
        <div class="col-md-4 meta-section">
            <h5>📌 Post Info</h5>
            <p><strong>Author:</strong> <?= htmlspecialchars($post['username']) ?></p>
            <p><strong>Created At:</strong> <?= date('d M Y, h:i A', strtotime($post['created_at'])) ?></p>
        </div>

        <!-- Right Column: Title & Content -->
        <div class="col-md-8 content-section">
            <h4><?= htmlspecialchars($post['title']) ?></h4>
            <hr>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
    </div>

    <div class="text-end back-btn">
        <a href="manage_post.php" class="btn btn-outline-dark">🔙 Back to Manage Post</a>
    </div>
</div>
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
