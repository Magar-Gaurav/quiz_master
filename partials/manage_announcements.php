<?php
session_start();
include '../connection/db.php';

$message = "";

// Handle add announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_announcement') {
  $msg = trim($_POST['message']);
  if ($msg !== "") {
    $stmt = $conn->prepare("INSERT INTO announcements (message, created_at) VALUES (?, NOW())");
    $stmt->bind_param("s", $msg);
    if ($stmt->execute()) {
      $message = "<div class='alert success'>Announcement posted successfully!</div>";
    } else {
      $message = "<div class='alert error'>Error posting announcement: " . $stmt->error . "</div>";
    }
    $stmt->close();
  } else {
    $message = "<div class='alert error'>Message cannot be empty.</div>";
  }
}

// Handle delete announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_announcement') {
  $announcementId = intval($_POST['announcement_id']);
  $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
  $stmt->bind_param("i", $announcementId);
  if ($stmt->execute()) {
    $message = "<div class='alert success'>Announcement deleted successfully!</div>";
  } else {
    $message = "<div class='alert error'>Error deleting announcement: " . $stmt->error . "</div>";
  }
  $stmt->close();
}

// Fetch announcements
$announcements = $conn->query("SELECT id, message, created_at FROM announcements ORDER BY created_at DESC");
?>

<style>
  body {
    background-color: #2d3748;
    font-family: 'Courier New', Courier, monospace;
  }

  .announcement-container {
    max-width: 700px;
    margin: auto;
    background: #2d3748;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    color: #f9fafb;
  }

  .announcement-container h2 {
    margin-bottom: 20px;
    color: #fff;
  }

  .announcement-list {
    list-style: none;
    padding: 0;
    margin-bottom: 20px;
  }

  .announcement-list li {
    background: #374151;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .announcement-list strong {
    color: #f9fafb;
  }

  .announcement-list span {
    font-size: 0.9em;
    color: #9ca3af;
    margin-left: 10px;
  }

  form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #e5e7eb;
  }

  form textarea {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #444;
    background: #374151;
    color: #f9fafb;
    margin-bottom: 12px;
    resize: vertical;
  }

  .submit-btn {
    background: #2563eb;
    color: #fff;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
  }

  .submit-btn:hover {
    background: #1d4ed8;
  }

  .delete-btn {
    background: #dc2626;
    color: #fff;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
  }

  .delete-btn:hover {
    background: #b91c1c;
  }

  .alert {
    padding: 12px;
    margin-bottom: 15px;
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

<div class="announcement-container">
  <h2>Manage Announcements</h2>

  <?= $message ?>

  <ul class="announcement-list">
    <?php while ($row = $announcements->fetch_assoc()): ?>
      <li>
        <div>
          <strong><?= htmlspecialchars($row['message']) ?></strong>
          <span><?= htmlspecialchars($row['created_at']) ?></span>
        </div>
        <!-- Delete form with direct address -->
        <form method="POST" action="/4th_sem_project/quiz_master/partials/manage_announcements.php" style="margin:0;">
          <input type="hidden" name="action" value="delete_announcement">
          <input type="hidden" name="announcement_id" value="<?= $row['id'] ?>">
          <button type="submit" class="delete-btn">Delete</button>
        </form>
      </li>
    <?php endwhile; ?>
  </ul>

  <!-- Add announcement form with direct address -->
  <form method="POST" action="/4th_sem_project/quiz_master/partials/manage_announcements.php">
    <input type="hidden" name="action" value="add_announcement">
    <label>New Announcement</label>
    <textarea name="message" required></textarea>
    <button class="submit-btn" type="submit">Post Announcement</button>
  </form>
</div>