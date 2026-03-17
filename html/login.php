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
  <link rel="stylesheet" href="../css/login.css">
</head>

<body>
  <header class="nav">
    <div class="nav-inner">
      <a href="./index.php" class="brand">
        <img src="../images/download.png" alt="Quiz Master Logo" class="logo-img">
        <span>Quiz Master</span>
      </a>
      <ul class="menu">
        <li><a href="./index.php" id="links">Home</a></li>
        <li><a href="./about.html" id="links">About</a></li>
        <li><a href="./contact.html" id="links">Contact</a></li>
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
    <div class="footer-content">
      <div class="footer-left">
        <strong>Quiz Master</strong>
        <p class="tagline">Challenge your mind, have fun!</p>
      </div>

      <div class="footer-right">
        <a href="https://facebook.com" target="_blank" id="fb"><i class="fab fa-facebook-f"></i></a>
        <a href="https://instagram.com" target="_blank" id="insta"><i class="fab fa-instagram"></i></a>
        <a href="https://twitter.com" target="_blank" id="twitter"><i class="fab fa-twitter"></i></a>
        <a href="https://linkedin.com" target="_blank" id="linkedin"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2025 QuizMaster. All rights reserved.</p>
    </div>
  </footer>
</body>

</html>