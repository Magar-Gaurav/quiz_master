<?php
session_start();
include '../connection/db.php';

$message = "";

if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}

$adminId = $_SESSION['admin_id'];

// Fetch current admin info
$stmt = $conn->prepare("SELECT id, username, email, profile_image FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newEmail    = trim($_POST['email']);
  $newPassword = trim($_POST['password']);
  $profileImage = $admin['profile_image'];

  if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    $message = "<div class='alert error'>Invalid email format</div>";
  } else {
    // Handle image upload
    if (!empty($_FILES['profile_image']['name'])) {
      $targetDir = __DIR__ . "/../uploads/";
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

      $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
      $targetFile = $targetDir . $fileName;

      if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        $profileImage = $fileName;
      } else {
        $message = "<div class='alert error'>Upload failed.</div>";
      }
    }

    // Update query
    if (empty($message)) {
      if (!empty($newPassword)) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET email=?, password=?, profile_image=? WHERE id=?");
        $stmt->bind_param("sssi", $newEmail, $hashed, $profileImage, $adminId);
      } else {
        $stmt = $conn->prepare("UPDATE admins SET email=?, profile_image=? WHERE id=?");
        $stmt->bind_param("ssi", $newEmail, $profileImage, $adminId);
      }

      if ($stmt->execute()) {
        $message = "<div class='alert success'>Profile updated successfully</div>";
      } else {
        $message = "<div class='alert error'>Update failed: " . $stmt->error . "</div>";
      }
      $stmt->close();
    }
  }
}
?>

<style>
  body {
    background-color: #2d3748;
    font-family: 'Courier New', Courier, monospace;

  }

  .profile-container {
    display: flex;
    gap: 20px;
    max-width: 700px;
    margin: auto;
    background: #2d3748;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    color: #f9fafb;
  }

  .profile-left img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
  }

  form label {
    display: block;
    margin-top: 10px;
    font-weight: 600;
    color: #e5e7eb;
  }

  form input {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #444;
    background: #374151;
    color: #f9fafb;
  }

  form button {
    margin-top: 12px;
    background: #2563eb;
    color: #fff;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
  }

  form button:hover {
    background: #1d4ed8;
  }

  .alert {
    padding: 12px;
    margin-top: 10px;
    border-radius: 6px;
    font-weight: 500;
  }

  .success {
    background: #d1e7dd;
    color: #0f5132;
  }

  .error {
    background: #f8d7da;
    color: #842029;
  }
</style>

<div class="profile-container">
  <div class="profile-left">
    <?php if (!empty($admin['profile_image'])): ?>
      <img src="../uploads/<?= htmlspecialchars($admin['profile_image']) ?>" alt="Profile Picture">
    <?php else: ?>
      <img src="https://via.placeholder.com/150" alt="Profile Picture">
    <?php endif; ?>
    <?= $message ?>
  </div>

  <div class="profile-right">
    <h2>Edit Profile</h2>
    <!-- Absolute path ensures correct routing -->
    <form method="POST" action="/4th_sem_project/quiz_master/partials/edit_profile.php" enctype="multipart/form-data">
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
      <label>New Password (leave blank to keep current)</label>
      <input type="password" name="password">
      <label>Profile Image</label>
      <input type="file" name="profile_image" accept="image/*">
      <button type="submit">Update Profile</button>
    </form>
  </div>
</div>