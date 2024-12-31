<?php
require 'db.php'; // Include the database connection

// Registration Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $ec_number = $_POST['ec_number'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Get the plain password
    $confirmPassword = $_POST['confirmPassword'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
        $modalMessage = $error;
        $modalTitle = "Registration Failed";
        $modalType = "danger";
    } else {
        // Hash the password for security after confirming the match
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Get current timestamp for the created_at field
        $created_at = date('Y-m-d H:i:s');

        // Insert the new user into the user_login_tb table
        $stmt = $conn->prepare("INSERT INTO user_login_tb (ec_number, name, surname, username, email, password, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$ec_number, $name, $surname, $username, $email, $hashedPassword, $created_at])) {
            $modalMessage = "Registration successful! Please login.";
            $modalTitle = "Success";
            $modalType = "success";
        } else {
            $modalMessage = "Something went wrong. Please try again.";
            $modalTitle = "Registration Failed";
            $modalType = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            margin-bottom: 50px;
        }
        .container {
            max-width: 500px;
            padding-top: 20px;
        }
        .register-box {
            border: 1px solid #ccc;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 0.25rem;
            height: 35px;
        }
        .btn {
            background-color: #e87f0a;
            border: none;
            height: 40px;
        }
        .btn:hover {
            background-color: #d6740a;
        }
        p.mt-3 {
            font-size: 14px;
        }
        .top-link {
            text-align: center;
            margin-bottom: 10px;
        }
        .top-link a {
            color: #e87f0a;
            text-decoration: none;
        }
    </style>
</head>
<body>

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

    <!-- Registration Form inside a bordered box -->
    <div class="register-box">
        <h2 class="text-center">Register</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="ec_number" class="form-label">E.C Number</label>
                <input type="text" class="form-control" id="ec_number" name="ec_number" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="mt-3 text-center">Already have an account? <a href="userlogin.php">Login here</a></p>
    </div>
</div>

<!-- Modal for Registration Status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel"><?php echo isset($modalTitle) ? $modalTitle : ''; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo isset($modalMessage) ? $modalMessage : ''; ?>
            </div>
            <div class="modal-footer">
                <?php if (isset($modalType) && $modalType == 'success'): ?>
                    <a href="userlogin.php" class="btn btn-primary">Go to Login</a>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle modal display -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('statusModal'), {
        keyboard: false
    });

    // Show modal if there's a message
    <?php if (isset($modalMessage)): ?>
        myModal.show();
    <?php endif; ?>
</script>

</body>
</html>
