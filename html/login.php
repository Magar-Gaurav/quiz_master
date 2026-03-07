<?php
session_start();
require_once '../connection/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $email = trim(strtolower($_POST['email']));
  $password = $_POST['password'];

  /* ================= ADMIN LOGIN ================= */
  $stmt = $conn->prepare("SELECT id, username, email, password FROM admins WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $adminResult = $stmt->get_result();

  if ($adminResult->num_rows === 1) {
    $admin = $adminResult->fetch_assoc();

    if (password_verify($password, $admin['password'])) {

      session_regenerate_id(true);

      $_SESSION['admin_id']   = $admin['id'];
      $_SESSION['admin_name'] = $admin['username'];
      $_SESSION['admin_email'] = $admin['email'];

      header("Location: ../partials/dashboard_home.php");
      exit();
    }
  }
  $stmt->close();

  /* ================= USER LOGIN ================= */
  $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $userResult = $stmt->get_result();

  if ($userResult->num_rows === 1) {
    $user = $userResult->fetch_assoc();

    if (password_verify($password, $user['password'])) {

      session_regenerate_id(true);

      $_SESSION['user_id']    = $user['id'];
      $_SESSION['user_name']  = $user['name'];
      $_SESSION['user_email'] = $user['email'];

      header("Location: ../student/dashboard.php");
      exit();
    }
  }
  $stmt->close();

  $message = "<div class='message-box error'>Invalid email or password.</div>";
}
?>
<!-- HTML form stays the same -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Quiz App</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #1f2937;
      color: #f9fafb;
      margin: 0;
    }

    .nav {
      background: #111827;
      padding: 10px 20px;
    }

    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .brand {
      display: flex;
      align-items: center;
      text-decoration: none;
      color: #fff;
      font-weight: 600;
    }

    .brand img {
      height: 40px;
      margin-right: 10px;
    }

    .menu {
      list-style: none;
      display: flex;
      gap: 20px;
    }

    .menu a {
      text-decoration: none;
      color: #d1d5db;
    }

    .login-container {
      max-width: 400px;
      margin: 60px auto;
      background: #2d3748;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    }

    .login-form h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .login-form label {
      display: block;
      margin: 10px 0 6px;
      font-weight: 600;
    }

    .login-form input {
      width: 100%;
      padding: 10px;
      border: 1px solid #444;
      border-radius: 6px;
      background: #374151;
      color: #f9fafb;
      margin-bottom: 12px;
    }

    .login-form button {
      width: 100%;
      padding: 12px;
      background: #2563eb;
      border: none;
      border-radius: 6px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
    }

    .login-form button:hover {
      background: #1d4ed8;
    }

    .signup-link {
      text-align: center;
      margin-top: 15px;
    }

    .message-box {
      position: relative;
      padding: 12px 16px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-weight: 500;
      font-size: 15px;
      text-align: center;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .close-btn {
      position: absolute;
      top: 6px;
      right: 10px;
      background: none;
      border: none;
      font-size: 18px;
      font-weight: bold;
      color: inherit;
      cursor: pointer;
    }

    .footer {
      background: #111827;
      color: #9ca3af;
      padding: 20px;
      text-align: center;
      margin-top: 40px;
      display: block;
      bottom: 0;
    }

    .footer a {
      color: #9ca3af;
      margin: 0 8px;
      text-decoration: none;
    }
  </style>
</head>

<body>
  <header class="nav">
    <div class="nav-inner">
      <a href="./index.php" class="brand">
        <img src="../images/download.png" alt="Quiz Master Logo" class="logo-img">
        <span>Quiz Master</span>
      </a>
      <ul class="menu">
        <li><a href="index.html">Home</a></li>
        <li><a href="./about.html">About</a></li>
        <li><a href="./contact.html">Contact</a></li>
      </ul>
    </div>
  </header>

  <main>
    <div class="login-container">
      <?php echo $message; ?>

      <form class="login-form" method="POST" action="">
        <h2>Login to Quiz App</h2>

        <label for="email">Email</label>
        <input type="email" name="email" placeholder="you@example.com" required />

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Enter your password" required />

        <div id="login">
          <button type="submit">Login</button>
        </div>

        <p class="signup-link">Don't have an account? <a href="./signup.php">Sign up</a></p>
      </form>
    </div>
  </main>

  <footer class="footer">
    <p>© 2025 QuizMaster. All rights reserved.</p>
    <div>
      <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
      <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
      <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
      <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
    </div>
  </footer>
</body>

</html>