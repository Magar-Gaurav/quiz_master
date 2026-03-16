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
      position: sticky;
      top: 0;
      width: 100%;
      background: #050d1b;

      z-index: 1000;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    }

    .nav-inner {
      max-width: 1200px;
      margin: auto;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: white;
      font-size: 20px;
      font-weight: 600;
    }

    .logo-img {
      width: 3vw;
    }

    .menu {
      list-style: none;
      display: flex;
      gap: 25px;
      align-items: center;
    }

    .menu a {
      color: white;
      text-decoration: none;
      font-size: 15px;
      transition: .3s;
    }

    .nav-btn {
      border: none;
      padding: 8px 18px;
      border-radius: 6px;
      background-color: #cd0a4b;
      box-shadow: 0 8px 0 #0005, 0 6px 0 #cd0a4b;
      transition: 0.3s ease-in-out;
      cursor: pointer;

      &:hover {
        transform: translateY(2px);
        box-shadow: 0 6px 0 #0005, 0 4px 0 #cd0a4b;
      }
    }

    .login-container {
      max-width: 400px;
      margin: 60px auto;
      background: #2d3748;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    }

    .login-form{
      display:flex;
      flex-direction: column;
      justify-content: center;
      gap:1.02rem;
      margin: 0 20px 0 15px;
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
      max-width: 360px;
      padding: 10px;
      border-radius: 6px;
      background: #374151;
      color: #f9fafb;
      margin-bottom: 12px;
      outline: none;
      border: none;
    }

    .login-form button {
      width: 70%;
      padding: 12px;
      margin-left: 15%;
      border: none;
      border-radius: 6px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      background-color: #cd0a4b;
      box-shadow: 0 8px 0 #0005, 0 6px 0 #cd0a4b;
      transition: 0.3s ease-in-out;
      cursor: pointer;

      &:hover {
        transform: translateY(2px);
        box-shadow: 0 6px 0 #0005, 0 4px 0 #cd0a4b;
      }
    }


    .signup-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .signup-link a {
      color: teal;
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
      background: #050d1b;
      padding: 20px 20px;
      margin-top: 60px;
    }

    .footer-left {
      font-size: 20px;
    }

    .footer-content {
      max-width: 1200px;
      margin: auto;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .footer-right a {
      margin-left: 10px;
      font-size: 25px;
      color: white;
    }

    .footer-bottom {
      text-align: center;
      margin-top: 20px;
      opacity: .8;
    }

    #fb:hover {
      color: #1877F2;
      /* text-shadow: 0 0 8px #1877F2, 0 0 16px rgba(24, 119, 242, 0.6); */
    }

    #insta:hover {
      background: linear-gradient(45deg, #405DE6, #833AB4, #C13584, #E1306C, #FD1D1D, #F56040, #FCAF45);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      /* text-shadow: 0 0 6px #833AB4, 0 0 12px #FD1D1D, 0 0 18px #FCAF45; */
    }

    #twitter:hover {
      color: #1DA1F2;
      /* text-shadow: 0 0 8px #1DA1F2; */
      transform: scale(1.2);
    }

    #linkedin:hover {
      color: #0A66C2;
      /* text-shadow: 0 0 8px #0A66C2; */
      transform: scale(1.2);
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
        <li><a href="./index.php">Home</a></li>
        <li><a href="./about.html">About</a></li>
        <li><a href="./contact.html">Contact</a></li>
        <button class="nav-btn"><a href="./login.php" class="btn">Login</a></button>
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