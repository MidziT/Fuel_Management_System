<?php
require 'db.php'; // Include database connection

// Start session for managing user sessions
session_start();

// Redirect if session is not set (i.e., user is not logged in)
if (!isset($_SESSION['ec_number'])) {
    header('Location: userlogin.php'); // Redirect to login page if user is not logged in
    exit();
}

// Fetch user data using the stored EC number from the session
$ec_number = $_SESSION['ec_number'];
$stmt = $conn->prepare("SELECT * FROM user_login_tb WHERE ec_number = ?");
$stmt->execute([$ec_number]);
$user = $stmt->fetch();

// Check if user exists in the database (to avoid session hijacking)
if (!$user) {
    // If the EC number doesn't exist, destroy the session and redirect to login
    session_destroy();
    header('Location: userlogin.php');
    exit();
}

// Update password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if old password matches the stored password
    if (password_verify($old_password, $user['password'])) {
        // Check if new password and confirm password match
        if ($new_password == $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update password in the database
            $update_stmt = $conn->prepare("UPDATE user_login_tb SET password = ? WHERE ec_number = ?");
            $update_stmt->execute([$hashed_password, $ec_number]);

            $success_message = "Password updated successfully!";
        } else {
            $error_message = "New password and confirm password do not match.";
        }
    } else {
        $error_message = "Old password is incorrect.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            margin: 0;
            background-color: #eef2f3;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 80px;
            z-index: 1;
        }
        .btn-change-password {
            background-color: #e87f0a;
            color: white;
            border: none;
            border-radius: 12px; /* This creates rounded corners */
            padding: 10px 20px;  /* Optional: Adjusts padding for a more balanced look */
            font-size: 1rem;     /* Optional: Adjusts the font size */
            transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }

        .btn-change-password:hover {
            background-color: #d67808; /* Slightly darker shade for hover effect */
        }


        .sidebar a {
            text-decoration: none;
            color: white;
            width: 100%;
            padding: 12px;
            text-align: center;
            font-size: 0.9rem;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar i {
            display: block;
            font-size: 1.3em;
        }

        .header {
            background-color: #e87f0a;
            padding: 10px;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 2;
        }

        .header h1 {
            font-size: 1.8rem;
            margin: 0;
        }

        .header-username {
            color: white;
            font-size: 1.2rem;
            margin-right: 20px;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            flex: 1;
        }

        .container {
            max-width: 600px;
            margin: auto;
        }
        
        footer {
            width: 100%;
            background-color: white;
            color: grey;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            left: 0;
            font-size: x-small;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <a href="dashboard2.php"><i class="bi bi-house-door"></i><span> Dashboard</span></a>
    <a href="add.php"><i class="bi bi-plus-circle"></i><span> Replenish Fuel</span></a>
    <a href="drawn.php"><i class="bi bi-cloud-arrow-down"></i><span> Dispense Fuel</span></a>
    <a href="condition_service.php"><i class="bi bi-cloud-arrow-down"></i><span> Condition Of Service</span></a>
    <a href="activity.php"><i class="bi bi-clock-history"></i><span> Recent Activities</span></a>
    <a href="reports.php"><i class="bi bi-file-earmark-bar-graph"></i><span> Reports</span></a> <!-- New Reports link -->
</div>



<!-- Header -->
<div class="header">
    <h1>Fuel Management System</h1>
    <div class="dropdown">
        <button class="btn btn-link dropdown-toggle text-white" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo htmlspecialchars($user['username']); ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="change-password.php">Change Password</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h3>Change Password</h3>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="change-password.php">
            <div class="mb-3">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-change-password">Change Password</button>


        </form>
    </div>
</div>

<footer>
    Powered By Midzi-Tech
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
