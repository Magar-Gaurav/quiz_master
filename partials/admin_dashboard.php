<?php
session_start();
require_once '../connection/db.php';

/* ================= PROTECT ADMIN ================= */
if (!isset($_SESSION['admin_id'])) {
  header("Location: ../html/login.php");
  exit();
}

$adminId = $_SESSION['admin_id'];

/* ================= FETCH ADMIN ================= */
$stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
  session_destroy();
  header("Location: ../html/login.php");
  exit();
}

$adminName = $admin['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Quiz Master</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root {
      --primary: #2563eb;
      --sidebar: #111827;
      --bg: #1f2937;
      --card: #2d3748;
      --text: #f9fafb;
      --muted: #9ca3af;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Courier New', Courier, monospace;
      background: var(--bg);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: var(--sidebar);
      color: #fff;
      padding: 20px;
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100%;
      left: 0;
      top: 0;
      transition: transform 0.3s ease;
      z-index: 1000;
    }

    .sidebar h2 {
      margin-bottom: 30px;
    }

    .sidebar a {
      text-decoration: none;
      color: #d1d5db;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: 0.2s;
      cursor: pointer;
    }

    .sidebar a:hover {
      background: var(--primary);
      color: #fff;
    }

    .logout {
      margin-top: auto;
    }

    /* Main */
    .main {
      margin-left: 250px;
      padding: 30px;
      width: 100%;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .menu-btn {
      background: var(--primary);
      border: none;
      color: #fff;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
      display: none;
    }

    /* Content area */
    #content {
      background: var(--card);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
      min-height: 400px;
    }

    /* Responsive */
    @media(max-width:768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .main {
        margin-left: 0;
      }

      .menu-btn {
        display: inline-block;
      }
    }
  </style>
</head>

<body>

  <div class="sidebar" id="sidebar">
    <h2>Quiz Master</h2>
    <a onclick="loadPage('./dashboard_home.php')"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a onclick="loadPage('./manage_users.php')"><i class="fas fa-users"></i> Users</a>
    <a onclick="loadPage('./manage_quizzes.php')"><i class="fas fa-book"></i> Quizzes</a>
    <a onclick="loadPage('./manage_announcements.php')"><i class="fas fa-bullhorn"></i> Announcements</a>
    <a onclick="loadPage('./edit_profile.php')"><i class="fas fa-user-edit"></i> Edit Profile</a>
    <a href="./html/login.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="main">
    <div class="topbar">
      <button class="menu-btn" onclick="toggleSidebar()">☰ Menu</button>
      <div>Welcome, <?= htmlspecialchars($adminName) ?></div>
    </div>

    <div id="content">
      <h2>Welcome to the Admin Dashboard</h2>
      <p>Select a menu item to view its content.</p>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("show");
    }

    function loadPage(page) {
      document.getElementById("content").innerHTML = "<p>Loading...</p>";
      fetch(page)
        .then(response => {
          if (!response.ok) throw new Error("Page not found");
          return response.text();
        })
        .then(html => {
          document.getElementById("content").innerHTML = html;
        })
        .catch(err => {
          document.getElementById("content").innerHTML =
            "<div style='color:red;'>Error loading page: " + err.message + "</div>";
        });
    }
  </script>

</body>

</html>