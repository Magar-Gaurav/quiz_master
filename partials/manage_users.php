<?php
include __DIR__ . '/../connection/db.php';

$message = "";

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if ($name !== "" && $email !== "" && $password !== "") {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        if ($stmt->execute()) {
            $message = "<div style='color:green;font-weight:600;'>✅ User added successfully!</div>";
        } else {
            $message = "<div style='color:red;font-weight:600;'>❌ Error adding user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    if ($name !== "" && $email !== "") {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
        if ($stmt->execute()) {
            $message = "<div style='color:green;font-weight:600;'>✅ User updated successfully!</div>";
        } else {
            $message = "<div style='color:red;font-weight:600;'>❌ Error updating user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<div style='color:green;font-weight:600;'>🗑 User deleted successfully!</div>";
    } else {
        $message = "<div style='color:red;font-weight:600;'>❌ Error deleting user: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

$users = $conn->query("SELECT id, name, email FROM users");
?>
<style>
  body{
    background-color: #374151;
    color:white;
    font-family: 'Courier New', Courier, monospace;
  }
</style>
<div style="background:#2d3748;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.25);color:#f9fafb;">
  <h2>Manage Users</h2>
  <?= $message ?>
  <table style="width:100%;border-collapse:collapse;margin-top:15px;">
    <thead>
      <tr style="background:#374151;color:#f9fafb;">
        <th style="padding:12px;text-align:left;">ID</th>
        <th style="padding:12px;text-align:left;">Name</th>
        <th style="padding:12px;text-align:left;">Email</th>
        <th style="padding:12px;text-align:left;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $users->fetch_assoc()): ?>
        <tr style="border-bottom:1px solid #444;">
          <td style="padding:12px;"><?= $row['id'] ?></td>
          <td style="padding:12px;"><?= htmlspecialchars($row['name']) ?></td>
          <td style="padding:12px;"><?= htmlspecialchars($row['email']) ?></td>
          <td style="padding:12px;">
            <!-- Edit form -->
            <form method="POST" action="/4th_sem_project/quiz_master/partials/manage_users.php" style="display:inline;">
              <input type="hidden" name="action" value="edit_user">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
              <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
              <button type="submit" style="background:#2563eb;color:#fff;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Save</button>
            </form>

            <!-- Delete form -->
            <form method="POST" action="/4th_sem_project/quiz_master/partials/manage_users.php" style="display:inline;margin-left:10px;">
              <input type="hidden" name="action" value="delete_user">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" style="background:#dc2626;color:#fff;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <hr style="border:1px solid #444;margin:20px 0;">

  <!-- Add User Form -->
  <h3>Add New User</h3>
  <form method="POST" action="/4th_sem_project/quiz_master/partials/manage_users.php">
    <input type="hidden" name="action" value="add_user">
    <input type="text" name="name" placeholder="Enter name" required style="padding:8px;border-radius:6px;border:none;margin-right:10px;">
    <input type="email" name="email" placeholder="Enter email" required style="padding:8px;border-radius:6px;border:none;margin-right:10px;">
    <input type="password" name="password" placeholder="Enter password" required style="padding:8px;border-radius:6px;border:none;margin-right:10px;">
    <button type="submit" style="background:#16a34a;color:#fff;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;">Add User</button>
  </form>
</div>
