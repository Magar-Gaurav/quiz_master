<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Quiz App</title>
  <link rel="stylesheet" href="../css/dashboard.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #f1f8ff);
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
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }
    .logout-btn:hover {
      background: #a71d2a;
    }
    .quiz-links {
      margin-top: 30px;
    }
    .quiz-links a {
      display: inline-block;
      margin: 10px;
      padding: 12px 20px;
      background: #007bff;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.3s;
    }
    .quiz-links a:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> 🎉</h1>
    <p>You are now logged in to the Quiz App.</p>

    <div class="quiz-links">
      <a href="quiz.php">Start a Quiz</a>
      <a href="profile.php">View Profile</a>
    </div>

    <a href="dashboard.php?logout=true" class="logout-btn">Logout</a>
  </div>
</body>
</html>
