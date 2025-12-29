<?php
session_start();
include '../connection/db.php'; // include your DB connection

// Protect admin page
if (!isset($_SESSION['admin'])) {
  header("Location: ../html/login.php");
  exit();
}

$adminName  = $_SESSION['admin']; // "Administrator" from login
$adminEmail = "admin@gmail.com";  // dummy email

// Fetch users count
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];

// Dummy stats for quizzes and attempts
$totalQuizzes  = 12;
$totalAttempts = 80;

// Fetch users from DB
$users = [];
$result = $conn->query("SELECT id, name, email FROM users");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
}

// Dummy quizzes
$quizzes = [
  ["id" => 101, "title" => "Python Basics", "created" => "2024-04-02"],
  ["id" => 102, "title" => "Database Concepts", "created" => "2024-03-28"],
  ["id" => 103, "title" => "Operating Systems", "created" => "2024-03-26"],
];

// Dummy announcements
$announcements = [
  ["title" => "System Maintenance", "body" => "Scheduled downtime on April 10.", "date" => "2024-04-05"],
  ["title" => "New Quiz Added", "body" => "Python Basics quiz is now live.", "date" => "2024-04-02"],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Quiz Master</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary: #0d6efd;
      --success: #198754;
      --danger: #dc3545;
      --bg: #f4f6f8;
      --card-bg: #fff;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: var(--bg);
    }

    header {
      background: var(--primary);
      color: #fff;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .brand {
      font-size: 1.6rem;
      font-weight: 600;
    }

    nav {
      display: flex;
      gap: 10px;
    }

    nav a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      padding: 0.5rem 0.75rem;
      border-radius: 6px;
    }

    nav a:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .logout-btn {
      color: #fff;
      background: none;
      font-weight: 600;
    }

    .logout-btn:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 0 16px;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .card {
      background: var(--card-bg);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card h2 {
      margin: 0;
      font-size: 2rem;
    }

    .card.blue {
      background: var(--primary);
      color: #fff;
    }

    .card.green {
      background: var(--success);
      color: #fff;
    }

    .card.red {
      background: var(--danger);
      color: #fff;
    }

    .section {
      background: var(--card-bg);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th,
    td {
      padding: 10px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background: #f1f5f9;
    }

    .actions button,
    .btn-add {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .actions button:hover,
    .btn-add:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .btn-edit {
      background: #0d6efd;
      color: #fff;
    }

    .btn-edit:hover {
      background: #0b5ed7;
    }

    .btn-delete {
      background: #dc3545;
      color: #fff;
    }

    .btn-delete:hover {
      background: #bb2d3b;
    }

    .btn-add {
      background: #198754;
      color: #fff;
      margin-bottom: 10px;
    }

    .btn-add:hover {
      background: #157347;
    }

    /* Hamburger styling */
    .menu-toggle {
      display: none;
    }

    .hamburger {
      display: none;
      cursor: pointer;
      width: 30px;
      height: 22px;
      flex-direction: column;
      justify-content: space-between;
    }

    .hamburger span,
    .hamburger span::before,
    .hamburger span::after {
      display: block;
      background: #fff;
      height: 3px;
      border-radius: 2px;
      position: relative;
    }

    .hamburger span::before,
    .hamburger span::after {
      content: "";
      position: absolute;
      left: 0;
      width: 100%;
    }

    .hamburger span::before {
      top: -8px;
    }

    .hamburger span::after {
      top: 8px;
    }

    @media (max-width:768px) {
      nav.menu {
        display: none;
        flex-direction: column;
        background: var(--primary);
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        padding: 10px;
      }

      nav.menu a {
        text-align: center;
        margin: 3px 0;
        padding: 8px;
        border-radius: 6px;
      }

      nav.menu a:not(.logout-btn):hover {
        background: rgba(255, 255, 255, 0.3);
      }

      .menu-toggle:checked~nav.menu {
        display: flex;
      }

      .hamburger {
        display: flex;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="brand">Quiz Master Admin</div>
    <input type="checkbox" id="menu-toggle" class="menu-toggle">
    <label for="menu-toggle" class="hamburger"><span></span></label>
    <nav class="menu">
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="manage_users.php">Users</a>
      <a href="manage_quizzes.php">Quizzes</a>
      <a href="../html/login.php" class="logout-btn">Logout</a>
    </nav>
  </header>
  <div class="container">

    <!-- Stats -->
    <div class="stats">
      <div class="card blue">Total Users<br>
        <h2><?= $totalUsers ?></h2>
      </div>
      <div class="card green">Total Quizzes<br>
        <h2><?= $totalQuizzes ?></h2>
      </div>
      <div class="card red">Total Attempts<br>
        <h2><?= $totalAttempts ?></h2>
      </div>
    </div>

    <!-- Manage Users -->
    <div class="section">
      <h3>Manage Users</h3>
      <button class="btn-add"><i class="fas fa-user-plus"></i> Add User</button>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td class="actions">
              <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>

              <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <!-- Manage Quizzes -->
    <div class="section">
      <h3>Manage Quizzes</h3>
      <button class="btn-add"><i class="fas fa-plus"></i> Add Quiz</button>
      <table>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
        <?php foreach ($quizzes as $q): ?>
          <tr>
            <td><?= $q['id'] ?></td>
            <td><?= htmlspecialchars($q['title']) ?></td>
            <td><?= $q['created'] ?></td>
            <td class="actions">
              <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
              <button class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <!-- Announcements -->
    <div class="section">
      <h3>Announcements</h3>
      <button class="btn-add"><i class="fas fa-bullhorn"></i> Add Announcement</button>
      <ul>
        <?php foreach ($announcements as $an): ?>
          <li><strong><?= htmlspecialchars($an['title']) ?>:</strong> <?= htmlspecialchars($an['body']) ?> (<?= $an['date'] ?>)</li>
        <?php endforeach; ?>
      </ul>
    </div>

  </div>
</body>

</html>