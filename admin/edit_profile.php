<?php
session_start();
include '../connection/db.php';

// Protect admin page
if (!isset($_SESSION['admin'])) {
  header("Location: ../html/login.php");
  exit();
}

$adminName = $_SESSION['admin'] ?? '';
$message = "";

// Fetch admin details (make sure your admins table has profile_image column)
$stmt = $conn->prepare("SELECT id, username, email, profile_image FROM admins WHERE username = ?");
$stmt->bind_param("s", $adminName);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
  $newEmail    = trim($_POST['email']);
  $newPassword = trim($_POST['password']);
  $profileImage = $admin['profile_image']; // keep old image by default

  // Handle profile image upload
  if (!empty($_FILES['profile_image']['name'])) {
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
      $profileImage = $fileName;
    }
  }

  if (!empty($newPassword)) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admins SET email=?, password=?, profile_image=? WHERE id=?");
    $stmt->bind_param("sssi", $newEmail, $hashedPassword, $profileImage, $admin['id']);
  } else {
    $stmt = $conn->prepare("UPDATE admins SET email=?, profile_image=? WHERE id=?");
    $stmt->bind_param("ssi", $newEmail, $profileImage, $admin['id']);
  }

  if ($stmt->execute()) {
    $message = "Profile updated successfully!";
    $admin['email'] = $newEmail;
    $admin['profile_image'] = $profileImage;
  } else {
    $message = "Error updating profile.";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile | Quiz Master</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --primary:#0d6efd; --bg:#f4f6f8; --card:#fff; --text:#222;
      --success-bg:#d1e7dd; --success-text:#0f5132;
      --error-bg:#f8d7da; --error-text:#842029;
    }
    body.dark { --bg:#121212; --card:#1e1e1e; --text:#f4f4f4; }
    body { margin:0; font-family:'Poppins',sans-serif; background:var(--bg); color:var(--text); }

    header { background:var(--primary); color:#fff; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; position:relative; }
    .brand { font-weight:600; font-size:1.2rem; }
    nav#navLinks { display:flex; gap:10px; }
    nav#navLinks a { color:#fff; text-decoration:none; font-weight:500; padding:6px 10px; border-radius:6px; }
    nav#navLinks a:hover { background:rgba(255,255,255,0.15); }
    .toggle-btn { background:none; border:2px solid #fff; color:#fff; padding:6px 10px; border-radius:6px; cursor:pointer; }

    .hamburger { display:none; flex-direction:column; justify-content:center; align-items:center; gap:5px; cursor:pointer; width:30px; height:24px; }
    .hamburger span { width:25px; height:3px; background:#fff; border-radius:2px; transition:0.3s; }
    .hamburger.active span:nth-child(1) { transform:rotate(45deg) translate(5px,5px); }
    .hamburger.active span:nth-child(2) { opacity:0; }
    .hamburger.active span:nth-child(3) { transform:rotate(-45deg) translate(6px,-6px); }

    @media(max-width:768px){
      .hamburger { display:flex; }
      nav#navLinks { display:none; flex-direction:column; align-items:center; background:var(--primary); position:absolute; top:60px; left:0; right:0; padding:10px; }
      nav#navLinks.show { display:flex; }
      nav#navLinks a { text-align:center; margin:8px 0; width:100%; }
    }

    .wrap { max-width:900px; margin:24px auto; padding:0 16px; }
    .card { background:var(--card); border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.08); padding:20px; }
    h2 { margin:0 0 12px; }

    .profile-section { display:flex; align-items:flex-start;}
    .profile-pic { width:200px; height:200px; border-radius:50%; object-fit:cover; background:#ccc; flex-shrink:0;}
    .form-section { flex:1; }

    label { display:block; margin:10px 0 6px; font-weight:600; text-align:left; }
    .field { width:100%; max-width:520px; padding:10px; border:1px solid #ccc; border-radius:6px; margin-bottom:12px; }
    .submit-btn { width:100%; max-width:540px; padding:10px; border:none; border-radius:6px; background:var(--primary); color:#fff; font-weight:600; cursor:pointer; }
    .submit-btn:hover { background:#0b5ed7; }

    .alert { position:relative; padding:12px 40px 12px 12px; border-radius:8px; margin-bottom:15px; }
    .alert.success { background:var(--success-bg); color:var(--success-text); }
    .alert.error { background:var(--error-bg); color:var(--error-text); }
    .close-btn { position:absolute; top:8px; right:12px; background:none; border:none; font-size:18px; font-weight:bold; cursor:pointer; color:inherit; }

    @media(min-width:769px){
        .profile-section{gap:5rem;}
        .profile-pic{width: 200px;height:200px;margin:12% 2%;}

    }
    @media(max-width:768px){
      .profile-section { flex-direction:column; align-items:center; text-align:left; }
      .form-section { width:100%; }
      .field, .submit-btn { max-width:95%; }
    }
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

<div class="wrap">
  <div class="card">
    <h2>Edit Profile</h2>

    <?php if (!empty($message)): ?>
      <div class="alert <?= strpos($message,'Error')!==false ? 'error' : 'success' ?>">
        <?= htmlspecialchars($message) ?>
        <button class="close-btn" onclick="this.parentElement.style.display='none'">&times;</button>
      </div>
    <?php endif; ?>

    <?php if ($admin): ?>
      <div class="profile-section">
        <?php if (!empty($admin['profile_image'])): ?>
          <img src="../uploads/<?= htmlspecialchars($admin['profile_image']) ?>" alt="Profile Picture" class="profile-pic">
        <?php else: ?>
          <img src="https://via.placeholder.com/110" alt="Profile Picture" class="profile-pic">
        <?php endif; ?>

        <div class="form-section">
          <form method="POST" enctype="multipart/form-data">
            <label>Username</label>
            <input class="field" type="text" value="<?= htmlspecialchars($admin['username']) ?>" disabled>

            <label>Email</label>
            <input class="field" type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

            <label>New password (leave blank to keep current)</label>
            <input class="field" type="password" name="password" placeholder="Enter new password">

            <label>Profile Image</label>
            <input class="field" type="file" name="profile_image" accept="image/*">

            <button class="submit-btn" type="submit" name="update_profile">Update profile</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div class="alert error">
        Admin profile not found.
        <button class="close-btn" onclick="this.parentElement.style.display='none'">&times;</button>
      </div>
    <?php endif; ?>
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
