<?php
session_start();
include '../connection/db.php';

// Protect user dashboard
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user'];
$userEmail = ""; 
$userId = 0;

// Fetch user info
$res = $conn->query("SELECT id, email, status FROM users WHERE name='$userName' LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $userId = $row['id'];
    $userEmail = $row['email'];
    $userStatus = $row['status'];
}

// Fetch available quizzes
$quizzes = $conn->query("SELECT id, title, category_id, duration_minutes, total_marks, status 
                         FROM quizzes WHERE status='enabled' ORDER BY created_at DESC");

// Fetch recent attempts
$attempts = $conn->query("SELECT a.id, q.title AS quiz_title, a.score, a.created_at
                          FROM attempts a
                          LEFT JOIN quizzes q ON q.id=a.quiz_id
                          WHERE a.user_id=$userId
                          ORDER BY a.created_at DESC
                          LIMIT 5");

// Fetch announcements
$announcements = $conn->query("SELECT title, body, created_at FROM announcements ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Dashboard | Quiz App</title>
  <link rel="stylesheet" href="../css/user.css" />
  <style>
    body { font-family: 'Segoe UI', sans-serif; background:#f6f9fc; margin:0; }
    header { background:#0d6efd; color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; }
    .brand { font-weight:700; }
    .nav a { color:#fff; margin-left:15px; text-decoration:none; }
    .container { max-width:1000px; margin:24px auto; padding:0 16px; }
    .card { background:#fff; border-radius:10px; padding:16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom:20px; }
    h2 { color:#0d6efd; margin-top:0; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
    th { background:#f1f5f9; }
    .logout-btn { background:#dc3545; color:#fff; padding:8px 12px; border-radius:6px; text-decoration:none; }
    .quiz-btn { background:#0d6efd; color:#fff; padding:6px 10px; border-radius:6px; text-decoration:none; }
  </style>
</head>
<body>
<header>
  <div class="brand">Quiz Master</div>
  <nav class="nav">
    <span>Welcome, <?= htmlspecialchars($userName) ?></span>
    <a href="user_dashboard.php?logout=true" class="logout-btn">Logout</a>
  </nav>
</header>
<div class="container">

  <!-- Profile -->
  <div class="card">
    <h2>Your Profile</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($userName) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($userEmail) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($userStatus) ?></p>
  </div>

  <!-- Available Quizzes -->
  <div class="card">
    <h2>Available Quizzes</h2>
    <table>
      <thead><tr><th>Title</th><th>Duration</th><th>Marks</th><th>Action</th></tr></thead>
      <tbody>
        <?php if ($quizzes && $quizzes->num_rows > 0): ?>
          <?php while ($q = $quizzes->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($q['title']) ?></td>
              <td><?= htmlspecialchars($q['duration_minutes']) ?> min</td>
              <td><?= htmlspecialchars($q['total_marks']) ?></td>
              <td><a href="start_quiz.php?id=<?= $q['id'] ?>" class="quiz-btn">Start</a></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4">No quizzes available right now.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent Attempts -->
  <div class="card">
    <h2>Your Recent Attempts</h2>
    <table>
      <thead><tr><th>Quiz</th><th>Score</th><th>Date</th></tr></thead>
      <tbody>
        <?php if ($attempts && $attempts->num_rows > 0): ?>
          <?php while ($a = $attempts->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($a['quiz_title']) ?></td>
              <td><?= htmlspecialchars($a['score']) ?></td>
              <td><?= htmlspecialchars($a['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="3">No attempts yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Announcements -->
  <div class="card">
    <h2>Announcements</h2>
    <?php if ($announcements && $announcements->num_rows > 0): ?>
      <ul>
        <?php while ($an = $announcements->fetch_assoc()): ?>
          <li><strong><?= htmlspecialchars($an['title']) ?>:</strong> <?= htmlspecialchars($an['body']) ?> (<?= $an['created_at'] ?>)</li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p>No announcements at the moment.</p>
    <?php endif; ?>
  </div>

</div>
</body>
</html>
