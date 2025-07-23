<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .btn-icon {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h3 class="mb-0">🛡️ Admin Panel</h3>
            </div>
            <div class="card-body">
                <h5 class="mb-3">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</h5>
                <span class="badge bg-dark mb-3">Role: Admin</span>
                <p class="mb-4">You have full access to manage users and posts.</p>

                <div class="d-flex gap-3">
                    <a href="manage_user.php" class="btn btn-primary btn-icon">
                        👥 Manage Users
                    </a>
                    <a href="manage_post.php" class="btn btn-secondary btn-icon">
                        📝 Manage Posts
                    </a>
                    <a href="index.php" class="btn btn-outline-dark btn-icon">
                        🔙 Back to Dashboard
                    </a>
                </div>
            </div>
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
