<?php
session_start();
include '../connection/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = "";
$selectedQuizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : null;

/* =========================
   HANDLE QUIZ SUBMISSION
========================= */
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

        $selected = isset($_POST['answer'][$qid]) ? intval($_POST['answer'][$qid]) : null;

        if ($selected) {
            $check = $conn->query("SELECT is_correct FROM options WHERE id=$selected AND question_id=$qid");
            if ($check && $check->num_rows > 0) {
                $row = $check->fetch_assoc();
                if ($row['is_correct'] == 1) {
                    $score++;
                }
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO quiz_history (user_id, quiz_id, score, attempted_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $userId, $quizId, $score);
    $stmt->execute();
    $stmt->close();

    $message = "<div class='message success'>You scored $score out of $total!</div>";
    $selectedQuizId = $quizId;
}

/* =========================
   FETCH QUIZZES
========================= */
$quizzes = $conn->query("SELECT id, title FROM quizzes");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>
<link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="container">

    <h2>Available Quizzes</h2>

    <div class="quiz-list">
        <?php while($quiz = $quizzes->fetch_assoc()): ?>
            <a href="?quiz_id=<?= $quiz['id'] ?>" class="quiz-card">
                <?= htmlspecialchars($quiz['title']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <?= $message ?>

    <?php if ($selectedQuizId): 

        $quizInfo = $conn->query("SELECT title FROM quizzes WHERE id=$selectedQuizId")->fetch_assoc();
    ?>

        <div class="quiz-section">
            <h2><?= htmlspecialchars($quizInfo['title']) ?></h2>

            <form method="POST">
                <input type="hidden" name="quiz_id" value="<?= $selectedQuizId ?>">

                <?php
                $questions = $conn->query("SELECT id, question_text FROM questions WHERE quiz_id=$selectedQuizId");

                while ($q = $questions->fetch_assoc()):
                ?>
                    <div class="question-card">
                        <h3><?= htmlspecialchars($q['question_text']) ?></h3>

                        <?php
                        $opts = $conn->query("SELECT id, option_text FROM options WHERE question_id=".$q['id']);
                        while ($opt = $opts->fetch_assoc()):
                        ?>
                            <label class="option">
                                <input type="radio" 
                                       name="answer[<?= $q['id'] ?>]" 
                                       value="<?= $opt['id'] ?>" required>
                                <span><?= htmlspecialchars($opt['option_text']) ?></span>
                            </label>
                        <?php endwhile; ?>

                    </div>
                <?php endwhile; ?>

                <button type="submit" class="submit-btn">Submit Quiz</button>
            </form>
        </div>

    <?php endif; ?>

</div>
</body>
</html>