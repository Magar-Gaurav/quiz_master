<?php
session_start();
include '../connection/db.php';

// Protect admin page
if (!isset($_SESSION['admin'])) {
  header("Location: ../html/login.php");
  exit();
}

$adminName = $_SESSION['admin'];

// Fetch admin details from DB
$stmt = $conn->prepare("SELECT email, profile_image FROM admins WHERE username = ?");
$stmt->bind_param("s", $adminName);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();
$stmt->close();

$adminEmail = $adminData['email'] ?? 'Not set';
$adminImage = !empty($adminData['profile_image'])
                ? "../uploads/" . $adminData['profile_image']
                : "https://via.placeholder.com/80";

// Stats
$totalUsers   = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$totalQuizzes = $conn->query("SELECT COUNT(*) AS count FROM quizzes")->fetch_assoc()['count'];
$totalAttempts= 0; // later track attempts
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Quiz Master</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary: #0d6efd;
      --success: #198754;
      --danger: #dc3545;
      --bg: #f4f6f8;
      --card-bg: #fff;
      --text: #000;
    }
    body.dark {
      --bg: #121212;
      --card-bg: #1e1e1e;
      --text: #f4f4f4;
    }
    body {
      margin:0; font-family:'Poppins',sans-serif;
      background:var(--bg); color:var(--text);
      transition: background 0.3s, color 0.3s;
    }
    header {
      background:var(--primary); color:#fff;
      padding:15px 20px; display:flex; justify-content:space-between; align-items:center;
      position:relative;
    }
    .brand { font-size:1.4rem; font-weight:600;}
    nav { display:flex; gap:10px;}
    nav a { color:#fff; text-decoration:none; font-weight:500; padding:6px 10px; border-radius:6px;}
    nav a:hover { background:rgba(255,255,255,0.15);}
    .toggle-btn { background:none; border:2px solid #fff; color:#fff; padding:6px 10px; border-radius:6px; cursor:pointer;}
    .toggle-btn:hover { background:rgba(255,255,255,0.2); }

    /* Hamburger */
    .hamburger {
      display:none;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      gap:5px;
      cursor:pointer;
      width:30px;
      height:24px;
      z-index:1000;
    }
    .hamburger span {
      width:25px; height:3px; background:#fff; border-radius:2px; transition:0.3s;
    }
    .hamburger.active span:nth-child(1) { transform:rotate(45deg) translate(5px,5px); }
    .hamburger.active span:nth-child(2) { opacity:0; }
    .hamburger.active span:nth-child(3) { transform:rotate(-45deg) translate(6px,-6px); }

    @media(max-width:768px){
      .hamburger { display:flex; }
      nav {
        display:none;
        flex-direction:column;
        align-items:center;
        background:var(--primary);
        position:absolute;
        top:60px; left:0; right:0;
        padding:10px;
      }
      nav.show { display:flex; }
      nav a { text-align:center; margin:8px 0; width:100%; }
    }

    .container { max-width:1200px; margin:20px auto; padding:0 16px;}
    .card { background:var(--card-bg); border-radius:10px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.08); margin-bottom:20px;}
    .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px;}
    .card.blue { background:var(--primary); color:#fff;}
    .card.green { background:var(--success); color:#fff;}
    .card.red { background:var(--danger); color:#fff;}
    /* Profile Card */
    .profile-card { display:flex; align-items:center; gap:20px;}
    .profile-card img { width:80px; height:80px; border-radius:50%; object-fit:cover; background:#ccc;}
    .profile-info h3 { margin:0;}
    .btn-edit { background:var(--primary); color:#fff; border:none; padding:8px 14px; border-radius:6px; cursor:pointer;}
    .btn-edit:hover { background:#0b5ed7;}
  </style>
</head>
<body>
<header>
  <div class="brand">Quiz Master Admin</div>
  <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
  <nav id="navLinks">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_users.php">Users</a>
    <a href="manage_quizzes.php">Quizzes</a>
    <a href="manage_announcements.php">Announcements</a>
    <a href="edit_profile.php">Edit Profile</a>
    <a href="../html/login.php">Logout</a>
    <button class="toggle-btn" onclick="toggleDarkMode()" id="darkToggle">🌙 Dark Mode</button>
  </nav>
</header>

<div class="container">

  <!-- Profile Card -->
  <div class="card profile-card">
    <img src="<?= htmlspecialchars($adminImage) ?>" alt="Admin Avatar">
    <div class="profile-info">
      <h3><?= htmlspecialchars($adminName) ?></h3>
      <p><?= htmlspecialchars($adminEmail) ?></p>
      <a href="./edit_profile.php"><button class="btn-edit"><i class="fas fa-user-edit"></i> Edit Profile</button></a>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="card blue">Total Users<br><h2><?= $totalUsers ?></h2></div>
    <div class="card green">Total Quizzes<br><h2><?= $totalQuizzes ?></h2></div>
    <div class="card red">Total Attempts<br><h2><?= $totalAttempts ?></h2></div>
  </div>

</div>

<script>
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('active');
  navLinks.classList.toggle('show');
});

document.addEventListener('click', function(event) {
  const isClickInsideMenu = navLinks.contains(event.target);
  const isClickOnHamburger = hamburger.contains(event.target);
  if (!isClickInsideMenu && !isClickOnHamburger) {
    navLinks.classList.remove('show');
    hamburger.classList.remove('active');
  }
});

function toggleDarkMode() {
  document.body.classList.toggle('dark');
  const isDark = document.body.classList.contains('dark');
  localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
  document.getElementById('darkToggle').textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
}

window.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('darkMode');
  if (saved === 'enabled') {
    document.body.classList.add('dark');
    document.getElementById('darkToggle').textContent = '☀️ Light Mode';
  } else {
    document.getElementById('darkToggle').textContent = '🌙 Dark Mode';
  }
});
</script>
</body>
</html>
