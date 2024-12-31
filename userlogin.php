<?php
require 'db.php'; // Include database connection

// Start session for managing user sessions
session_start();

// Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Sanitize and validate the inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Check if both fields are not empty
    if (empty($username) || empty($password)) {
        $error = "Username and Password cannot be empty!";
    } else {
        // Fetch user from the user_login_tb table using username
        $stmt = $conn->prepare("SELECT * FROM user_login_tb WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables for the logged-in user
            $_SESSION['ec_number'] = $user['ec_number']; // Store EC number in session
            $_SESSION['username'] = $user['username'];   // Store username in session

            // Redirect to dashboard2.php
            header('Location: dashboard2.php');
            exit();
        } else {
            // Show an error message if login fails
            $error = "Invalid username or password!";
        }
    }
}

// If the user is already logged in, redirect them to the dashboard directly
if (isset($_SESSION['ec_number'])) {
    header('Location: dashboard2.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .container {
            max-width: 500px;
            padding-top: 50px;
        }
        .login-box {
            border: 1px solid #ccc;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
        }
        .btn {
            background-color: #e87f0a;
            border: none;
        }
        .btn:hover {
            background-color: #d6740a;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        .error {
            color: red;
        }
        .top-link {
            text-align: center;
            margin-bottom: 20px;
        }
        .top-link a {
            color: #e87f0a;
            text-decoration: none;
        }



        .main-link {
            position: absolute;
            top: 10px;
            left: 10px;
            text-decoration: none;
            color: white;
            font-size: 1rem;
            background-color: #343a40;
            padding: 8px 12px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }
        .main-link:hover {
            background-color: #e87f0a;
        }
        .main-link i {
            font-size: 1.2rem;
        }



    </style>
</head>
<body>


 <!-- Link to Main Page -->
 <a href="main.php" class="main-link">
        <i class="bi bi-arrow-left"></i>
        Back to Main
</a>

<!-- Top link to add a logo or message -->
<div class="top-link">
    <a href="#"> <!-- Link to homepage or another relevant page -->
        <img src="images/zhrc_logo123.png" alt="ZHRC Logo" style="width: 100px; height: auto;"> <!-- Adjust width and height as needed -->
    </a>
</div>

<div class="container">
    <!-- Display error messages -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Login Form inside a bordered box -->
    <div class="login-box">
        <h2 class="text-center">Login</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

</body>
</html>
