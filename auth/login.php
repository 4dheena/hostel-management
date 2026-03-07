<?php
session_start();
require_once '../database/db_connect.php';

$error = "";

/* If already logged in, redirect automatically */
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: ../admin/dashboard.php");
            exit;
        case 'warden':
            header("Location: ../warden/dashboard.php");
            exit;
        case 'student':
            header("Location: ../student/dashboard.php");
            exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['user_id']) || empty($_POST['password'])) {
        $error = "Please enter both User ID and Password.";
    } else {

        $user_id  = trim($_POST['user_id']);
        $password = $_POST['password'];

        $stmt = $conn->prepare(
            "SELECT user_id, password, role FROM users WHERE user_id = ?"
        );
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role']    = $user['role'];

                switch ($user['role']) {
                    case 'admin':
                        header("Location: ../admin/dashboard.php");
                        exit;
                    case 'warden':
                        header("Location: ../warden/dashboard.php");
                        exit;
                    case 'student':
                        header("Location: ../student/dashboard.php");
                        exit;
                    default:
                        $error = "Unauthorized role.";
                }

            } else {
                $error = "Invalid User ID or Password.";
            }

        } else {
            $error = "Invalid User ID or Password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login | Hostel Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }
        .login-box {
            width: 350px;
            margin: 80px auto;
            padding: 25px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 6px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <input type="text" name="user_id" placeholder="User ID" required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>