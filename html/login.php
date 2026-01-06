<?php
session_start();
include '../connection/db.php'; // your database connection

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // sanitize inputs
  $email    = strtolower(trim($conn->real_escape_string($_POST['email'])));
  $password = $_POST['password'];

  // Hard-coded admin credentials
  $admin_email    = "admin@gmail.com";
  $admin_password = "adminuser@123";

  // Check if admin login
  if ($email === $admin_email && $password === $admin_password) {
    $_SESSION['admin'] = "Administrator";
    header("Location: ../admin/admin_dashboard.php");
    exit();
  }

  // Otherwise, check normal users in DB
  $sql = "SELECT * FROM users WHERE email='$email'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // verify hashed password
    if (password_verify($password, $user['password'])) {
      $_SESSION['user']  = $user['name'];   // store user name
      $_SESSION['email'] = $user['email'];  // store user email
      header("Location: dashboard.php");    // redirect to user dashboard
      exit();
    } else {
      $message = "
            <div class='message-box error'>
                Invalid password. Please try again.
                <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
            </div>";
    }
  } else {
    $message = "
        <div class='message-box error'>
            No account found with that email.
            <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
        </div>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Quiz App</title>
  <link rel="stylesheet" href="../css/login.css" />
  <!-- font awesome cdn -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- google fonts -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_right_alt" />
  <style>
    .message-box {
      position: relative;
      padding: 12px 16px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-weight: 500;
      font-size: 15px;
      text-align: center;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
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
  </style>
</head>

<body>
  <header class="nav">
    <div class="nav-inner">
      <!-- Brand -->
      <a href="./index.html" class="brand">
        <img src="../images/download.png" alt="Quiz Master Logo" class="logo-img">
        <span>Quiz Master</span>
      </a>
      <!-- Hamburger -->
      <input type="checkbox" id="menu-toggle" class="menu-toggle">
      <label for="menu-toggle" class="hamburger"><span></span></label>
      <!-- Menu -->
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