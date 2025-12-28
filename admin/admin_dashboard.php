<?php
session_start();

// Protect admin dashboard
if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Quiz App</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f1f8ff;
      margin: 0;
      padding: 0;
    }
    .dashboard-container {
      max-width: 800px;
      margin: 60px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      text-align: center;
    }
    h1 {
      color: #007bff;
    }
    .logout-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 18px;
      background: #dc3545;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }
    .logout-btn:hover {
      background: #a71d2a;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Welcome, <?php echo $_SESSION['admin']; ?> 👑</h1>
    <p>This is the Admin Dashboard. From here you can manage quizzes, users, and settings.</p>

    <a href="admin_dashboard.php?logout=true" class="logout-btn">Logout</a>
  </div>
</body>
</html>
