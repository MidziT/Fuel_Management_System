<?php
require 'db.php';

// Start session for managing user sessions
session_start();

// Redirect if session is not set (i.e., user is not logged in)
if (!isset($_SESSION['ec_number'])) {
    header('Location: userlogin.php'); // Redirect to login page if user is not logged in
    exit();
}

// Enable error mode for PDO to catch exceptions more clearly
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch user data using the stored EC number from the session
$ec_number = $_SESSION['ec_number'];
$stmt = $conn->prepare("SELECT * FROM user_login_tb WHERE ec_number = ?");
$stmt->execute([$ec_number]);
$user = $stmt->fetch();

// Check if user exists in the database (to avoid session hijacking)
if (!$user) {
    session_destroy();
    header('Location: userlogin.php');
    exit();
}

// Display user-specific data on the dashboard
$username = $user['username'];
$name = $user['name'];
$surname = $user['surname'];
$email = $user['email'];

$message = "";
$invalidSerialFlag = false;
$successFlag = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture the serial number range from the input field
    $serial_range = $_POST['serial_number']; // This is the input field where the range is captured
    list($start_serial, $end_serial) = explode(" To ", $serial_range); // Split the range into start and end

    $fuel_type = $_POST['fuel_type'];
    $total_amount = $_POST['amount'];  
    $car_registration_number = $_POST['car_registration'];
    $issued_to = $_POST['issued_to'];
    $purpose_of_issue = $_POST['purpose_of_issue'];
    $otp_verification = $_POST['otp_verification'];
    $destination = $_POST['destination'];
    $kilometres = $_POST['kilometres'];

    // Calculate the correct amount per coupon
    $amount_per_coupon = 20;

    try {
        // Start the transaction
        $conn->beginTransaction();

        // Generate a list of serial numbers in the specified range
        $serials = generateSerialRange($start_serial, $end_serial);
        foreach ($serials as $serial) {
            // Check if each coupon exists in add_fuel_tb
            $stmt = $conn->prepare('
                SELECT 
                    serial_number,
                    fuel_type,
                    amount AS available_amount,
                    supplier,
                    added_at
                FROM 
                    add_fuel_tb
                WHERE 
                    serial_number = ? AND fuel_type = ?
            ');
            $stmt->execute([$serial, $fuel_type]);
            $coupon = $stmt->fetch();

            if ($coupon) {
                // Insert the transaction into fuel_transaction_tb
                $current_timestamp = date('Y-m-d H:i:s');
                $stmt = $conn->prepare('INSERT INTO fuel_transaction_tb 
                    (serial_number, fuel_type, amount, car_registration_number, issued_to, purpose_of_issue, email_address, otp_verification, ec_number, transaction_time, destination, kilometres) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

                // Execute the transaction insert query with the per-coupon amount
                $stmt->execute([
                    $serial,
                    $fuel_type,
                    $amount_per_coupon,
                    $car_registration_number,
                    $issued_to,
                    $purpose_of_issue,
                    $email,  // Use email from the logged-in user
                    $otp_verification,
                    $ec_number,
                    $current_timestamp,
                    $destination,
                    $kilometres
                ]);

                // After inserting the transaction, delete the coupon from add_fuel_tb
                $deleteStmt = $conn->prepare('DELETE FROM add_fuel_tb WHERE serial_number = ?');
                $deleteStmt->execute([$serial]);

                $successFlag = true;
            } else {
                $invalidSerialFlag = true;
                throw new Exception("Coupon $serial not found or invalid fuel type.");
            }
        }
        // Commit the transaction if all coupons in the range are successfully drawn
        $conn->commit();
        $message = "Fuel drawn successfully for coupon range from $start_serial to $end_serial.";

    } catch (Exception $e) {
        // Rollback and report error
        $conn->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

/**
 * Helper function to generate a list of serial numbers in the specified range.
 * Adjusts for typical alphanumeric serial number formats.
 *
 * @param string $start The starting serial number.
 * @param string $end The ending serial number.
 * @return array List of serial numbers in the specified range.
 */
function generateSerialRange($start, $end) {
    $range = [];
    $startNum = (int)substr($start, -6); // Get the numeric part of the start serial
    $endNum = (int)substr($end, -6);     // Get the numeric part of the end serial
    $prefix = substr($start, 0, -6);     // Get the prefix of the serial

    for ($i = $startNum; $i <= $endNum; $i++) {
        $range[] = $prefix . str_pad($i, 6, '0', STR_PAD_LEFT);
    }
    return $range;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draw Fuel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            text-align: center;
            font-family: 'Roboto', sans-serif;
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
            width: calc(100% - 250px);
        }

        form {
            background: white;
            padding: 10px; /* Reduced padding by 10% */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 630px; /* Increased max width */
            margin: auto;
        }

        input, select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #e87f0a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #d77f0a;
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
        <img src="images/zhrc_logo123.png" alt="ZHRC Logo" style="width: 100px; height: auto;">
        <h1>Fuel Management System</h1>
        <div class="dropdown">
            <button class="btn btn-link dropdown-toggle text-white" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($username); ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#accountModal">Account</a></li>
                <li><a class="dropdown-item" href="change-password.php">Change Password</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="text-center">Dispense Fuel</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $successFlag ? 'success' : 'danger'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Serial Number Range Input -->
            <input type="text" class="form-control" name="serial_number" placeholder="Serial Number Range (e.g., PU006B1234512 To PU006B1234600)" required>

            <select name="fuel_type" required>
                <option value="" disabled selected>Select Fuel Type</option>
                <option value="petrol">Petrol</option>
                <option value="diesel">Diesel</option>
            </select>
            <input type="number" name="amount" placeholder="Amount (in Liters)" required>
            <input type="text" name="car_registration" placeholder="Car Registration Number" required>
            <input type="text" name="issued_to" placeholder="Issued To" required>
            <input type="text" name="purpose_of_issue" placeholder="Purpose of Issue" required>
            <input type="email" name="email_address" placeholder="Email Address" required>
            <input type="text" name="otp_verification" placeholder="OTP Verification" required>
            <input type="text" name="destination" placeholder="Destination" required>
            <input type="number" name="kilometres" placeholder="Kilometres" required>
            <button type="submit">Dispense Fuel</button>
        </form>
    </div>

    <footer style="width: 100%; background-color: white; color: grey; text-align: center; padding: 10px; position: fixed; bottom: 0; left: 0; font-size: x-small;">
        Powered By Midzi-Tech
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
