<?php
session_start();
include '../connection/db.php';

// Protect admin page
if (!isset($_SESSION['admin'])) {
    header("Location: ../html/login.php");
    exit();
}

$message = "";

// Add Question (no quiz select)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $option_a      = $_POST['option_a'];
    $option_b      = $_POST['option_b'];
    $option_c      = $_POST['option_c'];
    $option_d      = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    // ⚠️ Hardcode quiz_id or handle it elsewhere
    $quiz_id = 1; // example: default quiz ID

    $stmt = $conn->prepare("INSERT INTO questions 
    (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);
    $stmt->execute();
    $stmt->close();

    $message = "Question added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Question | Quiz Master</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary: #0d6efd;
            --bg: #f4f6f8;
            --card: #fff;
            --text: #222;
        }

        body.dark {
            --bg: #121212;
            --card: #1e1e1e;
            --text: #f4f4f4;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        header {
            background: var(--primary);
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-weight: 600;
            font-size: 1.2rem;
        }

        nav {
            display: flex;
            gap: 10px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 6px;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .toggle-btn {
            background: none;
            border: 2px solid #fff;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Hamburger */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #fff;
            transition: 0.3s;
        }

        @media(max-width:768px) {
            nav {
                display: none;
                flex-direction: column;
                align-items: center;
                /* ✅ center horizontally */
                background: var(--primary);
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                padding: 10px;
                transition: max-height 0.3s ease;
                overflow: hidden;
                max-height: 0;
            }

            nav.show {
                display: flex;
                max-height: 400px;
                /* ✅ smooth slide-down */
            }

            nav a {
                text-align: center;
                margin: 8px 0;
                width: 100%;
            }

            .hamburger {
                display: flex;
                justify-content: center;
                align-items: center;
            }
        }

        .container {
            max-width: 600px;
            margin: 24px auto;
            background: var(--card);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        input,
        select,
        button {
            width: 100%;
            max-width: 500px;
            margin: 8px auto;
            display: block;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background: #198754;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #157347;
        }

        .msg {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <header>
        <div class="brand">Quiz Master Admin</div>
        <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
        <nav id="navLinks">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="manage_users.php">Users</a>
            <a href="manage_quizzes.php">Quizzes</a>
            <a href="manage_announcements.php">Announcements</a>
            <a href="edit_profile.php">Edit Profile</a>
            <a href="../html/login.php">Logout</a>
            <button class="toggle-btn" onclick="toggleDarkMode()" id="darkToggle">🌙 Dark Mode</button>
        </nav>
    </header>

    <div class="container">
        <h2>Add Question</h2>
        <?php if (!empty($message)): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="question_text" placeholder="Question" required>
            <input type="text" name="option_a" placeholder="Option A" required>
            <input type="text" name="option_b" placeholder="Option B" required>
            <input type="text" name="option_c" placeholder="Option C" required>
            <input type="text" name="option_d" placeholder="Option D" required>

            <select name="correct_option" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <button type="submit" name="add_question">Add Question</button>
        </form>
    </div>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('show');
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark');
            const isDark = document.body.classList.contains('dark');
            document.getElementById('darkToggle').textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
        }
    </script>
</body>

</html>