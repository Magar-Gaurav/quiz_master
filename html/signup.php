<?php
session_start();
include '../connection/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name     = $conn->real_escape_string($_POST['name']);
  $email    = $conn->real_escape_string($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if email already exists
  $check = $conn->query("SELECT id FROM users WHERE email='$email'");
  if ($check->num_rows > 0) {
    $message = "
        <div class='message-box error'>
            Email already registered. Please use another.
            <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
        </div>";
  } else {
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
      $message = "
            <div class='message-box success'>
                Signup successful!.
                <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
            </div>";
    } else {
      $message = "
            <div class='message-box error'>
                Error: " . $conn->error . "
                <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
            </div>";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup | Quiz App</title>
  <link rel="stylesheet" href="../css/signup.css">
  <!-- font awesome cdn -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

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
      <a href="./index.php" class="brand">
        <img src="../images/download.png" alt="Quiz Master Logo" class="logo-img">
        <span>Quiz Master</span>
      </a>
      <ul class="menu">
        <li><a href="./index.php" id="links">Home</a></li>
        <li><a href="./about.html" id="links">About</a></li>
        <li><a href="./contact.html" id="links">Contact</a></li>
        <button class="nav-btn"><a href="./login.php" class="btn">Login</a></button>
      </ul>
    </div>
  </header>
  <main>

    <!-- Use the correct container class -->
    <div class="signup-container">
      <?php echo $message; ?>

      <!-- Use the correct form class -->
      <form class="signup-form" method="POST" action="">
        <h2>Create an Account</h2>

        <label for="name">Full Name</label>
        <input type="text" name="name" placeholder="Your name" required />

        <label for="email">Email</label>
        <input type="email" name="email" placeholder="you@example.com" required />

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Enter password" required />

        <button type="submit">Sign Up</button>
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
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