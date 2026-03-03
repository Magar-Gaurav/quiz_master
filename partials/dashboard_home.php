<?php
include __DIR__ . '/../connection/db.php';

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$totalQuizzes = $conn->query("SELECT COUNT(*) AS total FROM quizzes")->fetch_assoc()['total'] ?? 0;
$totalAttempts = $conn->query("SHOW TABLES LIKE 'leaderboard'")->num_rows
  ? $conn->query("SELECT COUNT(*) AS total FROM leaderboard")->fetch_assoc()['total'] ?? 0
  : 0;
?>
<div style="background:#2d3748;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.25);">
  <h2 style="margin-bottom:20px;">Dashboard Overview</h2>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;">
    <div style="background:#374151;padding:25px;border-radius:12px;text-align:center;">
      <h4 style="color:#9ca3af;margin-bottom:10px;">Total Users</h4>
      <h2><?= $totalUsers ?></h2>
    </div>
    <div style="background:#374151;padding:25px;border-radius:12px;text-align:center;">
      <h4 style="color:#9ca3af;margin-bottom:10px;">Total Quizzes</h4>
      <h2><?= $totalQuizzes ?></h2>
    </div>
    <div style="background:#374151;padding:25px;border-radius:12px;text-align:center;">
      <h4 style="color:#9ca3af;margin-bottom:10px;">Total Attempts</h4>
      <h2><?= $totalAttempts ?></h2>
    </div>
  </div>
</div>
