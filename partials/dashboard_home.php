<?php
session_start();
include '../connection/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../html/login.php");
    exit;
}

$section = $_GET['section'] ?? "overview";
$message = "";


if (isset($_POST['update_profile'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admins SET username=?,email=?,password=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $hashed, $_SESSION['admin_id']);
    $stmt->execute();

    $_SESSION['admin_name'] = $name;
    $_SESSION['admin_email'] = $email;

    $message = "Profile updated successfully";
}


if (isset($_POST['add_quiz'])) {

    $title = $_POST['quiz_title'];

    $stmt = $conn->prepare("INSERT INTO quizzes(title) VALUES(?)");
    $stmt->bind_param("s", $title);
    $stmt->execute();

    $message = "Quiz added";
}if (isset($_POST['delete_quiz'])) {
    $id = $_POST['quiz_id'];

    $stmt = $conn->prepare("DELETE FROM quiz_history WHERE quiz_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $message = "Quiz deleted";
}

if (isset($_POST['add_question'])) {

    $quiz_id = $_POST['quiz_id'];
    $question = $_POST['question'];
    $options = $_POST['options'];
    $correct = $_POST['correct'];

    $stmt = $conn->prepare("INSERT INTO questions(quiz_id,question_text) VALUES(?,?)");
    $stmt->bind_param("is", $quiz_id, $question);
    $stmt->execute();

    $qid = $stmt->insert_id;

    foreach ($options as $k => $opt) {

        $is_correct = ($k == $correct) ? 1 : 0;

        $stmt2 = $conn->prepare("INSERT INTO options(question_id,option_text,is_correct) VALUES(?,?,?)");
        $stmt2->bind_param("isi", $qid, $opt, $is_correct);
        $stmt2->execute();
    }

    $message = "Question added";
}

if (isset($_POST['add_user'])) {

    $name = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);
    $password = password_hash($_POST['user_password'], PASSWORD_DEFAULT);

    try {

        $stmt = $conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();

        $message = "User added successfully";
    } catch (mysqli_sql_exception $e) {

        if (str_contains($e->getMessage(), 'Duplicate')) {
            $message = "Email already exists";
        } else {
            $message = "Something went wrong";
        }
    }
}

if (isset($_POST['delete_user'])) {

    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM quiz_history WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $message = "User deleted";
}

if (isset($_POST['edit_user'])) {

    $user_id = $_POST['user_id'];
    $name = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);

    try {

        $stmt = $conn->prepare("UPDATE users SET name=?,email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();

        $message = "User updated successfully";
    } catch (mysqli_sql_exception $e) {

        if (str_contains($e->getMessage(), 'Duplicate')) {
            $message = "Email already exists";
        } else {
            $message = "Update failed";
        }
    }
}


$totalQuiz = $conn->query("SELECT COUNT(*) as t FROM quizzes")->fetch_assoc()['t'];
$totalQuestions = $conn->query("SELECT COUNT(*) as t FROM questions")->fetch_assoc()['t'];
$totalUsers = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];

$users = $conn->query("SELECT * FROM users");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="./styles.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <div class="dashboard">

        <div class="sidebar">

            <h2 class="logo">Quiz Master</h2>

            <a href="?section=overview" class="<?= $section == 'overview' ? 'active' : '' ?>">Dashboard</a>

            <a href="?section=quiz" class="<?= $section == 'quiz' ? 'active' : '' ?>">Quiz Topics</a>

            <a href="?section=questions" class="<?= $section == 'questions' ? 'active' : '' ?>">Questions</a>

            <a href="?section=users" class="<?= $section == 'users' ? 'active' : '' ?>">Users</a>

            <a href="?section=profile" class="<?= $section == 'profile' ? 'active' : '' ?>">Edit Profile</a>

            <div class="sidebar-bottom">

                <form action="../html/login.php" method="post">

                    <button class="logout">Logout</button>

                </form>

            </div>

        </div>


        <div class="content">

            <h1>Welcome Administrator</h1>

            <?php if ($message != "") { ?>

                <div class="message"><?php echo $message ?></div>

            <?php } ?>


            <?php if ($section == "overview") { ?>
                <h2 style="margin-bottom: 1.2rem;">Dashboard</h2>
                <div class="stats">

                    <div class="card">

                        <h3><?php echo $totalQuiz ?></h3>

                        <p>Total Quizzes</p>

                    </div>

                    <div class="card">

                        <h3><?php echo $totalQuestions ?></h3>

                        <p>Total Questions</p>

                    </div>

                    <div class="card">

                        <h3><?php echo $totalUsers ?></h3>

                        <p>Total Students</p>

                    </div>

                </div>

            <?php } ?>


            <?php if ($section == "quiz") { ?>

                <h2>Quiz Topics</h2>

                <form method="post">

                    <input type="text" name="quiz_title" placeholder="Quiz Title" required>

                    <button name="add_quiz">Add Quiz</button>

                </form>

                <table>

                    <tr>

                        <th>ID</th>

                        <th>Title</th>

                        <th>Action</th>

                    </tr>

                    <?php

                    $q = $conn->query("SELECT * FROM quizzes");

                    while ($row = $q->fetch_assoc()) {

                    ?>

                        <tr>

                            <td><?php echo $row['id'] ?></td>

                            <td><?php echo $row['title'] ?></td>

                            <td>

                                <form method="post">

                                    <input type="hidden" name="quiz_id" value="<?php echo $row['id'] ?>">

                                    <button name="delete_quiz" class="delete">Delete</button>

                                </form>

                            </td>

                        </tr>

                    <?php } ?>

                </table>

            <?php } ?>

            <?php if ($section == "questions") { ?>

                <div class="panel">

                    <h2>Add Question</h2>

                    <form method="post">

                        <select name="quiz_id">

                            <?php
                            $quizlist = $conn->query("SELECT * FROM quizzes");
                            while ($q = $quizlist->fetch_assoc()) {
                                echo "<option value='{$q['id']}'>{$q['title']}</option>";
                            }
                            ?>

                        </select>

                        <textarea name="question" placeholder="Enter question" required></textarea>

                        <input type="text" name="options[]" placeholder="Option 1" required>
                        <input type="text" name="options[]" placeholder="Option 2" required>
                        <input type="text" name="options[]" placeholder="Option 3" required>
                        <input type="text" name="options[]" placeholder="Option 4" required>

                        <select name="correct">
                            <option value="0">Correct Option 1</option>
                            <option value="1">Correct Option 2</option>
                            <option value="2">Correct Option 3</option>
                            <option value="3">Correct Option 4</option>
                        </select>

                        <button name="add_question">Add Question</button>

                    </form>

                </div>


                <div class="panel">

                    <h2>Question List</h2>

                    <table>

                        <tr>
                            <th>ID</th>
                            <th>Quiz</th>
                            <th>Question</th>
                            <th>Correct Answer</th>
                        </tr>

                        <?php

                        $q = $conn->query("
SELECT q.id,q.question_text,qu.title
FROM questions q
JOIN quizzes qu ON q.quiz_id=qu.id
");

                        while ($row = $q->fetch_assoc()) {

                            $correct = $conn->query("
SELECT option_text FROM options
WHERE question_id={$row['id']} AND is_correct=1
")->fetch_assoc();

                        ?>

                            <tr>

                                <td><?php echo $row['id'] ?></td>
                                <td><?php echo $row['title'] ?></td>
                                <td><?php echo $row['question_text'] ?></td>
                                <td><?php echo $correct['option_text'] ?></td>

                            </tr>

                        <?php } ?>

                    </table>

                </div>

            <?php } ?>

            <?php if ($section == "users") { ?>

                <h2>Users Management</h2>

                <form method="post" class="add-user">

                    <input type="text" name="user_name" placeholder="Name" required>

                    <input type="email" name="user_email" placeholder="Email" required>

                    <input type="password" name="user_password" placeholder="Password" required>

                    <button name="add_user">Add User</button>

                </form>

                <table class="users-table">

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Actions</th>

                    </tr>

                    <?php while ($u = $users->fetch_assoc()) { ?>

                        <tr>

                            <form method="post">

                                <td><?php echo $u['id'] ?></td>

                                <td>

                                    <span id="name_<?php echo $u['id'] ?>"><?php echo $u['name'] ?></span>

                                    <input type="text" name="user_name"
                                        value="<?php echo $u['name'] ?>"
                                        id="edit_name_<?php echo $u['id'] ?>"
                                        class="edit-field" style="display:none">

                                </td>

                                <td>

                                    <span id="email_<?php echo $u['id'] ?>"><?php echo $u['email'] ?></span>

                                    <input type="email" name="user_email"
                                        value="<?php echo $u['email'] ?>"
                                        id="edit_email_<?php echo $u['id'] ?>"
                                        class="edit-field" style="display:none">

                                </td>

                                <td class="actions">

                                    <input type="hidden" name="user_id" value="<?php echo $u['id'] ?>">

                                    <button type="button"
                                        class="edit-btn"
                                        onclick="enableEdit(<?php echo $u['id'] ?>)">

                                        <i class="fa-solid fa-pen"></i>

                                    </button>

                                    <button type="submit"
                                        name="edit_user"
                                        id="save_<?php echo $u['id'] ?>"
                                        class="save-btn"
                                        style="display:none">

                                        <i class="fa-solid fa-check"></i>

                                    </button>

                            </form>

                            <form method="post" style="display:inline">

                                <input type="hidden" name="user_id" value="<?php echo $u['id'] ?>">

                                <button name="delete_user"
                                    class="delete-btn"
                                    onclick="return confirm('Delete user?')">

                                    <i class="fa-solid fa-trash"></i>

                                </button>

                            </form>

                            </td>

                        </tr>

                    <?php } ?>

                </table>

            <?php } ?>


            <?php if ($section == "profile") { ?>

                <h2>Edit Profile</h2>

                <form method="post">

                    <label>Name</label>

                    <input type="text" name="name"
                        value="<?php echo $_SESSION['admin_name'] ?>" required>

                    <label>Email</label>

                    <input type="email" name="email"
                        value="<?php echo $_SESSION['admin_email'] ?>" required>

                    <label>Password</label>

                    <input type="password" name="password" required>

                    <button name="update_profile" id="update">Update Profile</button>

                </form>

            <?php } ?>

        </div>

    </div>

    <script>
        function enableEdit(id) {

            document.getElementById("name_" + id).style.display = "none";
            document.getElementById("email_" + id).style.display = "none";

            document.getElementById("edit_name_" + id).style.display = "inline";
            document.getElementById("edit_email_" + id).style.display = "inline";

            document.getElementById("save_" + id).style.display = "inline";

        }
    </script>

</body>

</html>