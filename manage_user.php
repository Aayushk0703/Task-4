<?php
session_start();
$deleted = isset($_GET['deleted']) ? (int)$_GET['deleted'] : 0;
$updated = isset($_GET['updated']) ? (int)$_GET['updated'] : 0;

require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit;
}

// Fetch all users
$sql = "SELECT id, username, email, role FROM users ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h3>👥 Manage Users</h3>
            </div>
            <div class="card-body">
                <p class="mb-3">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
                <span class="badge bg-dark mb-4">Role: Admin</span>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                     <tbody>
                        <?php if ($deleted === 1): ?>
                      <div class="alert alert-success alert-dismissible fade show" role="alert" id="deleteAlert">
             ✅ User deleted successfully!
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
    <?php endif; ?>
    <?php if ($updated === 1): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert" id="updateAlert">
        ✏️ User updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
 <script>
    setTimeout(() => {
        const deleteAlert = document.getElementById('deleteAlert');
        if (deleteAlert) deleteAlert.classList.remove('show');

        const updateAlert = document.getElementById('updateAlert');
        if (updateAlert) updateAlert.classList.remove('show');
    }, 3000);
 </script>

                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge <?= $user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span></td>
                            <td>
                    
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-success">Edit</a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="admin_panel.php" class="btn btn-outline-dark mt-3">🔙 Back to Admin Panel</a>
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
