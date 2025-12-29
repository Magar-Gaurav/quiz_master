<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = "John Doe";
}
$userName  = $_SESSION['user'];
$userEmail = $_SESSION['email'] ?? 'Not Available';

$totalQuizzes   = 8;
$totalAttempts  = 5;
$successRate    = 60;

$attempts = [
    ["quiz" => "Java Basics", "score" => 70, "date" => "2024-04-01", "status" => "Passed"],
    ["quiz" => "Database Concepts", "score" => 60, "date" => "2024-03-28", "status" => "Passed"],
    ["quiz" => "Operating Systems", "score" => 40, "date" => "2024-03-26", "status" => "Failed"],
];

$announcements = [
    ["title" => "New Quiz Available", "body" => "Try out the latest Java quiz!", "date" => "2024-04-02"],
    ["title" => "System Update", "body" => "Dashboard UI has been improved.", "date" => "2024-03-30"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard | Quiz App</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0d6efd;
      --success: #198754;
      --danger: #dc3545;
      --bg: #f4f6f8;
      --card-bg: #fff;
    }
    body { margin:0; font-family: 'Poppins', sans-serif; background: var(--bg); }
    header {
      position: sticky;
      top: 0;
      background: var(--primary);
      color: #fff;
      padding: 20px 40px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 1000;
    }
    .brand { font-size: 1.6rem; font-weight: 600; letter-spacing: 1px; }
    nav { display: flex; gap: 20px; align-items: center; }
    nav a { color: #fff; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
    nav a:hover { color: #ffd43b; }
    .logout-btn {
      background: #dc3545;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 600;
      transition: background 0.3s ease;
    }
    .logout-btn:hover { background: #a71d2a; }
    .container { max-width:1200px; margin:20px auto; padding:0 16px; }
    .stats { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:20px; }
    .card { background: var(--card-bg); border-radius:10px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    .card h2 { margin:0; font-size:2rem; }
    .card.blue { background: var(--primary); color:#fff; }
    .card.green { background: var(--success); color:#fff; }
    .card.red { background: var(--danger); color:#fff; }
    .section { background: var(--card-bg); padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.08); flex:1; }
    table { width:100%; border-collapse:collapse; margin-top:10px; }
    th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
    th { background:#f1f5f9; }
    .passed { color: var(--success); font-weight:bold; }
    .failed { color: var(--danger); font-weight:bold; }
    ul { padding-left:20px; }
    .flex-row { display:flex; gap:20px; flex-wrap:wrap; margin-bottom:20px; }
    @media (max-width:900px) {
      .flex-row { flex-direction:column; }
      nav { flex-direction: column; gap: 10px; }
    }
  </style>
</head>
<body>
<header>
  <div class="brand">Quiz Master</div>
  <nav>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="quizzes.php">Quizzes</a>
    <a href="profile.php">Profile</a>
    <a href="./login.php" class="logout-btn">Logout</a>
  </nav>
</header>
<div class="container">

  <div class="stats">
    <div class="card blue">Total Quizzes<br><h2><?= $totalQuizzes ?></h2></div>
    <div class="card green">Quizzes Attempted<br><h2><?= $totalAttempts ?></h2></div>
    <div class="card red">Success Rate<br><h2><?= $successRate ?>%</h2></div>
  </div>

  <div class="flex-row">
    <div class="section">
      <h3>Recent Attempts</h3>
      <table>
        <tr><th>Quiz Name</th><th>Score</th><th>Date</th><th>Status</th></tr>
        <?php foreach ($attempts as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['quiz']) ?></td>
            <td><?= $a['score'] ?></td>
            <td><?= $a['date'] ?></td>
            <td class="<?= strtolower($a['status']) ?>"><?= $a['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="section">
      <h3>Your Profile</h3>
      <p><strong>Name:</strong> <?= htmlspecialchars($userName) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($userEmail) ?></p>
    </div>
  </div>

  <div class="section">
    <h3>Announcements</h3>
    <ul>
      <?php foreach ($announcements as $an): ?>
        <li><strong><?= htmlspecialchars($an['title']) ?>:</strong> <?= htmlspecialchars($an['body']) ?> (<?= $an['date'] ?>)</li>
      <?php endforeach; ?>
    </ul>
  </div>

</div>
</body>
</html>
