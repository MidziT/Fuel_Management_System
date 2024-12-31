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
$loggedInUserEmail = $user['email'];  // This email is for reference only and won't be used in the transaction

$message = "";
$invalidSerialFlag = false;
$successFlag = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture the serial number (single or range) from the input field
    $serial_range = $_POST['serial_number']; // This is the input field where the serial number is captured

    // Check if the serial number is a range or a single serial number
    if (strpos($serial_range, " To ") !== false) {
        // It's a range, split it into start and end serial numbers
        list($start_serial, $end_serial) = explode(" To ", $serial_range);
        $serials = generateSerialRange($start_serial, $end_serial);  // Generate range of serial numbers
    } else {
        // It's a single serial number, just use it as is
        $serials = [$serial_range];  // Wrap the single serial in an array for consistency
    }

    $fuel_type = $_POST['fuel_type'];
    $issued_to = $_POST['issued_to'];
    $email = $_POST['email'];  // New input field for recipient's email
    $otp_verification = $_POST['otp_verification'];
  
    // Check if email is provided
    if (empty($email)) {
        $message = "Error: Email address is required.";
    } else {
        // Define the fixed amount per coupon
        $amount_per_coupon = 20;

        try {
            // Start the transaction
            $conn->beginTransaction();

            // Loop through the list of serial numbers
            foreach ($serials as $serial) {
                // Check if the coupon exists in add_fuel_tb
                $stmt = $conn->prepare('
                    SELECT 
                        serial_number,
                        fuel_type,
                        amount AS available_amount
                    FROM 
                        add_fuel_tb
                    WHERE 
                        serial_number = ? AND fuel_type = ?
                ');
                $stmt->execute([$serial, $fuel_type]);
                $coupon = $stmt->fetch();

                if ($coupon) {
                    // Insert the transaction into condition_service_tb
                    $stmt = $conn->prepare('INSERT INTO condition_service_tb 
                        (serial_number, fuel_type, amount, issued_to, email, otp_verification, ec_number, transaction_timestamp) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');

                    // Execute the transaction insert query with the per-coupon amount
                    $stmt->execute([
                        $serial,
                        $fuel_type,
                        $amount_per_coupon,   // Insert the amount
                        $issued_to,
                        $email,  // Use email of the person receiving the fuel
                        $otp_verification,
                        $ec_number
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
            $message = "Fuel dispensed successfully for coupon(s): " . implode(", ", $serials);

        } catch (Exception $e) {
            // Rollback and report error
            $conn->rollBack();
            $message = "Error: " . $e->getMessage();
        }
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
    <title>Fuel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
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
    <img src="images/zhrc_logo123.png" alt="ZHRC Logo" style="width: 100px; height: auto;"> <!-- Adjust width and height as needed -->
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

<!-- Modal for Account Details -->
<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accountModalLabel">Account Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="ecNumber" class="form-label">EC Number</label>
                        <input type="text" class="form-control" id="ecNumber" value="<?php echo htmlspecialchars($ec_number); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($name); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="surname" value="<?php echo htmlspecialchars($surname); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Logout Confirmation -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" type="button" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
        <h2 class="text-center">Condition Of Service</h2>

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
            <input type="number" name="amount_per_coupon" placeholder="Amount Per Coupon (in Liters)" required>
            <input type="text" name="issued_to" placeholder="Issued To" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="otp_verification" placeholder="OTP Verification" required>
            <button type="submit">Dispense Fuel</button>
        </form>

</div>

<footer style="width: 100%; background-color: white; color: grey; text-align: center; padding: 10px; position: fixed; bottom: 0; left: 0; font-size: x-small;">
    Powered By Midzi-Tech
</footer>

<!-- Include Bootstrap JS (Only Once) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


</body>
</html>
