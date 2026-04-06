<?php

session_start();
include '../connection/db.php';

$message = "";

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); 
}

$userId = $_SESSION['user_id'];
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';
$selectedQuizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : null;

$stmt = $conn->prepare("SELECT id, name, email, profile_pic, password FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$profileImage = "../images/default_profile.avif";

if (!empty($user['profile_pic'])) {
    $userImagePath = "../uploads/" . $user['profile_pic'];

    if (file_exists($userImagePath)) {
        $profileImage = $userImagePath;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $profilePic = $user['profile_pic'];

    if (!empty($_FILES['profile_pic']['name'])) {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (in_array($_FILES['profile_pic']['type'], $allowedTypes)) {

            $targetDir  = "../uploads/";
            $fileName   = time() . "_" . basename($_FILES["profile_pic"]["name"]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
                $profilePic = $fileName;
            }
        } else {
            $message = "<div class='message error'>Only JPG, JPEG, PNG allowed.</div>";
        }
    }

    $updatePassword = false;
    $hashedPassword = null;

    if (!empty($currentPassword) && !empty($newPassword)) {
        if (!password_verify($currentPassword, $user['password'])) {
            $message = "<div class='message error'>Current password is incorrect.</div>";
        } elseif (strlen($newPassword) < 6) {
            $message = "<div class='message error'>New password must be at least 6 characters.</div>";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    if (empty($message)) {
        if ($updatePassword) {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, profile_pic=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $profilePic, $hashedPassword, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, profile_pic=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $profilePic, $userId);
        }

        if ($stmt->execute()) {
            $message = "<div class='message success'>Profile updated successfully!</div>";

            $user['name'] = $name;
            $user['email'] = $email;
            $user['profile_pic'] = $profilePic;

            $profileImage = !empty($profilePic) && file_exists("../uploads/" . $profilePic)
                ? "../uploads/" . $profilePic
                : "../assets/images/default-avatar.png";
        } else {
            $message = "<div class='message error'>Error updating profile.</div>";
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {

    $quizId = intval($_POST['quiz_id']);
    $score = 0;

    $questions = $conn->query("SELECT id FROM questions WHERE quiz_id=$quizId");
    $questionIds = [];

    while ($q = $questions->fetch_assoc()) {
        $questionIds[] = $q['id'];
    }

    $total = count($questionIds);

    foreach ($questionIds as $qid) {

        $selected = isset($_POST['answer'][$qid]) ? intval($_POST['answer'][$qid]) : 0;

        if ($selected > 0) {

            $correctQuery = $conn->query("SELECT id FROM options WHERE question_id=$qid AND is_correct=1");

            if ($correctQuery && $correctQuery->num_rows > 0) {
                $correct = $correctQuery->fetch_assoc();

                if ($selected == $correct['id']) {
                    $score++;
                }
            }
        }
    }

    if ($score > $total) {
        $score = $total;
    }

    $stmt = $conn->prepare("INSERT INTO quiz_history (user_id, quiz_id, score, attempted_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $userId, $quizId, $score);
    $stmt->execute();
    $stmt->close();

    if ($score == $total) {
    $_SESSION['message'] = "<div class='message success'> Perfect! $score / $total</div>";
} elseif ($score >= $total / 2) {
    $_SESSION['message'] = "<div class='message success'> Good job! $score / $total</div>";
} else {
    $_SESSION['message'] = "<div class='message error'> Keep practicing! $score / $total</div>";
}

    header("Location: ?section=quizzes&quiz_id=$quizId");
    exit;
}

$quizzes = $conn->query("SELECT id, title FROM quizzes ORDER BY id DESC");
$history = $conn->query("SELECT q.title, h.score, h.attempted_at 
                         FROM quiz_history h 
                         JOIN quizzes q ON h.quiz_id=q.id 
                         WHERE h.user_id=$userId 
                         ORDER BY h.attempted_at DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="topbar">
        <h1>Student Dashboard</h1>
        <div class="profile" onclick="window.location.href='?section=edit_profile'">
            <img src="<?= $profileImage ?>?v=<?= time() ?>" alt="Profile">
        </div>
    </div>
    <div class="sidebar">
        <nav> <a href="?section=overview" class="<?= $section === 'overview' ? 'active' : '' ?>"><i class="fa-solid fa-gauge"></i><span>Overview</span></a> <a href="?section=quizzes" class="<?= $section === 'quizzes' ? 'active' : '' ?>"><i class="fa-solid fa-book-open"></i><span>Solve Quiz</span></a> <a href="?section=history" class="<?= $section === 'history' ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i><span>Quiz History</span></a> </nav> <a href="../html/login.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
    <div class="content"> <?= $message ?> <?php if ($section === 'overview'): ?> <div class="section">
                <h2>Overview</h2>
                <p>Welcome back! Use the sidebar to solve quizzes or view your history.</p>
            </div> <?php elseif ($section === 'quizzes'): ?> <div class="section">
                <h2>Available Quizzes</h2>
                <div class="quiz-list"> <?php while ($quiz = $quizzes->fetch_assoc()): ?> <a href="?section=quizzes&quiz_id=<?= $quiz['id'] ?>" class="quiz-card"> <?= htmlspecialchars($quiz['title']) ?> </a> <?php endwhile; ?> </div> <?php if ($selectedQuizId): $quizInfo = $conn->query("SELECT title FROM quizzes WHERE id=$selectedQuizId")->fetch_assoc(); ?> <div class="quiz-container">
                        <h2><?= htmlspecialchars($quizInfo['title']) ?></h2>
                        <form method="POST"> <input type="hidden" name="quiz_id" value="<?= $selectedQuizId ?>"> <?php $questions = $conn->query("SELECT id, question_text FROM questions WHERE quiz_id=$selectedQuizId");
                                                                                                                                                                                                                                                while ($q = $questions->fetch_assoc()): $opts = $conn->query("SELECT id, option_text FROM options WHERE question_id=" . $q['id']); ?> <div class="question-card">
                                    <h3><?= htmlspecialchars($q['question_text']) ?></h3> <?php while ($opt = $opts->fetch_assoc()): ?> <label class="option"> <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>" required> <span><?= htmlspecialchars($opt['option_text']) ?></span> </label> <?php endwhile; ?>
                                </div> <?php endwhile; ?> <button type="submit" class="submit-btn">Submit Quiz</button> </form>
                    </div> <?php endif; ?>
            </div> <?php elseif ($section === 'history'): ?> <div class="section">
                <h2>Quiz History</h2> <?php if ($history->num_rows > 0): ?> <table>
                        <thead>
                            <tr>
                                <th>Quiz</th>
                                <th>Score</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody> <?php while ($row = $history->fetch_assoc()): ?> <tr>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= $row['score'] ?></td>
                                    <td><?= htmlspecialchars($row['attempted_at']) ?></td>
                                </tr> <?php endwhile; ?> </tbody>
                    </table> <?php else: ?> <p>No quiz performed till now.</p> <?php endif; ?>
            </div> <?php elseif ($section === 'edit_profile'): ?> <div class="section edit-profile">
                <h2>Edit Profile</h2> <?php if ($user['profile_pic']): ?> <img src="../uploads/<?= htmlspecialchars($user['profile_pic']) ?>?v=<?= time() ?>" alt="Profile Picture"> <?php else: ?> <img src="../assets/images/default-avatar.png" alt="Profile Picture"> <?php endif; ?> <form method="POST" enctype="multipart/form-data"> <input type="hidden" name="action" value="update_profile"> <label>Name</label> <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required> <label>Email</label> <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required> <label>Current Password (only if changing password)</label> <input type="password" name="current_password"> <label>New Password</label> <input type="password" name="new_password"> <label>Profile Picture</label> <input type="file" name="profile_pic"> <button type="submit">Update Profile</button> </form>
            </div> <?php endif; ?> </div>
</body>

</html>