<?php
session_start();
include '../connection/db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: ../html/login.php");
  exit();
}

/* --- Add User --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check duplicate email
  $check = $conn->prepare("SELECT id FROM users WHERE email=?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $check->close();
    header("Location: manage_users.php?msg=exists");
    exit();
  }
  $check->close();

  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);
  $stmt->execute();
  $stmt->close();

  header("Location: manage_users.php?msg=added");
  exit();
}

/* --- Update User --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
  $userId   = intval($_POST['user_id']);
  $newName  = trim($_POST['name']);
  $newEmail = trim($_POST['email']);

  // Check duplicate email excluding current user
  $check = $conn->prepare("SELECT id FROM users WHERE email=? AND id<>?");
  $check->bind_param("si", $newEmail, $userId);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $check->close();
    header("Location: manage_users.php?msg=exists");
    exit();
  }
  $check->close();

  $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
  $stmt->bind_param("ssi", $newName, $newEmail, $userId);
  $stmt->execute();
  $stmt->close();

  header("Location: manage_users.php?msg=updated");
  exit();
}

/* --- Delete User --- */
if (isset($_GET['delete'])) {
  $userId = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $stmt->close();

  header("Location: manage_users.php?msg=deleted");
  exit();
}

/* --- Fetch Users --- */
$res = $conn->query("SELECT id, name, email FROM users ORDER BY id DESC");
$users = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$totalUsers = count($users);

/* --- Messages --- */
$message = "";
if (isset($_GET['msg'])) {
  if ($_GET['msg'] === 'added')   $message = "✅ User added successfully!";
  if ($_GET['msg'] === 'updated') $message = "✏️ User updated successfully!";
  if ($_GET['msg'] === 'deleted') $message = "🗑️ User deleted successfully!";
  if ($_GET['msg'] === 'exists')  $message = "⚠️ Email already exists. Please use another.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users | Quiz Master</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      --primary:#0d6efd; --bg:#f4f6f8; --card:#fff; --text:#222;
      --success-bg:#d1e7dd; --success-text:#0f5132;
    }
    body { font-family:'Poppins',sans-serif; background:var(--bg); margin:0; color:var(--text); }
    header { background:var(--primary); color:#fff; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; position:relative; }
    .brand { font-weight:600; font-size:1.2rem; }
    nav#navLinks { display:flex; gap:10px; }
    nav#navLinks a { color:#fff; text-decoration:none; padding:6px 10px; border-radius:6px; }
    nav#navLinks a:hover { background:rgba(255,255,255,0.15); }
    .hamburger { display:none; flex-direction:column; gap:5px; cursor:pointer; width:30px; height:24px; }
    .hamburger span { width:25px; height:3px; background:#fff; border-radius:2px; transition:.3s; }
    @media(max-width:768px){
      .hamburger { display:flex; }
      nav#navLinks { display:none; flex-direction:column; align-items:center; background:var(--primary); position:absolute; top:60px; left:0; right:0; padding:10px; }
      nav#navLinks.show { display:flex; }
      nav#navLinks a { width:100%; text-align:center; margin:6px 0; }
    }
    .container { max-width:1000px; margin:20px auto; background:var(--card); padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    h2 { margin-top:0; }
    .alert { background:var(--success-bg); color:var(--success-text); padding:12px; border-radius:8px; margin-bottom:15px; }
    form { margin-bottom:20px; }
    label { display:block; margin:10px 0 6px; font-weight:600; }
    input[type="text"], input[type="email"], input[type="password"] { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; margin-bottom:12px; }
    .btn { padding:8px 14px; border:none; border-radius:6px; cursor:pointer; }
    .btn-add { background:#198754; color:#fff; }
    .btn-add:hover { background:#157347; }
    .btn-delete { background:#dc3545; color:#fff; }
    .btn-delete:hover { background:#bb2d3b; }
    .table-wrap { margin-top:10px; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #ccc; padding:10px; text-align:left; }
    th { background:var(--primary); color:#fff; }
    @media(max-width:768px){
      .container { padding:15px; }
      .table-wrap { overflow-x:auto; }
      table { min-width:600px; }
      th, td { font-size:14px; padding:8px; }
    }
    @media(max-width:480px){
      .btn { width:100%; margin-top:6px; }
      h2 { font-size:1.2rem; }
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
  </nav>
</header>

<div class="container">
  <h2>Manage Users</h2>
  <p>Total Users: <strong><?= $totalUsers ?></strong></p>

  <?php if (!empty($message)): ?>
    <div class="alert"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- Add User Form -->
  <form method="POST">
    <h3>Add New User</h3>
    <label>Name</label>
    <input type="text" name="name" placeholder="Full Name" required>
    <label>Email</label>
    <input type="email" name="email" placeholder="Email" required>
    <label>Password</label>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="add_user" class="btn btn-add">Add User</button>
  </form>

  <!-- Edit User Form -->
  <?php if (isset($_GET['edit'])):
    $editId = intval($_GET['edit']);
    $getStmt = $conn->prepare("SELECT id, name, email FROM users WHERE id=?");
    $getStmt->bind_param("i", $editId);
    $getStmt->execute();
    $editRes = $getStmt->get_result();
    $editUser = $editRes->fetch_assoc();
    $getStmt->close();

    if ($editUser):
  ?>
  <form method="POST">
    <h3>Edit User</h3>
    <input type="hidden" name="user_id" value="<?= (int)$editUser['id'] ?>">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($editUser['name'] ?? '') ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($editUser['email'] ?? '') ?>" required>
    <button type="submit" name="update_user" class="btn btn-add">Update User</button>
  </form>
  <?php else: ?>
    <div class="alert">User not found.</div>
  <?php endif; endif; ?>

  <!-- Users Table -->
  <h3>All Users</h3>
  <div class="table-wrap">
    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= (int)$user['id'] ?></td>
          <td><?= htmlspecialchars($user['name'] ?? '[no name]') ?></td>
          <td><?= htmlspecialchars($user['email'] ?? '[no email]') ?></td>
          <td>
            <a href="manage_users.php?edit=<?= (int)$user['id'] ?>">
              <button class="btn btn-add">Edit</button>
            </a>
            <a href="manage_users.php?delete=<?= (int)$user['id'] ?>" onclick="return confirm('Delete this user?')">
              <button class="btn btn-delete">Delete</button>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
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
</script>
</body>
</html>
