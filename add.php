<?php
require 'db.php';

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

$message = ''; // Variable to hold the notification message

// Display user-specific data on the dashboard
$username = $user['username']; // Access the username from the database
$name = $user['name']; // Access the name from the database
$surname = $user['surname']; // Access the surname from the database
$email = $user['email']; // Access the email from the database


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_number_input = $_POST['serial_number'];
    $fuel_type = $_POST['fuel_type'];
    $amount = $_POST['amount'];
    $supplier = $_POST['supplier'];
    $email = $_POST['email'];
    $otp_verification = $_POST['otp_verification'];  // New field for OTP verification

    // Step 1: Handle serial number input - check if it's a range or single number
    $serial_numbers = [];

    // Validate serial number length (must be at least 13 characters)
    if (strlen($serial_number_input) < 13) {
        $message = "Invalid serial number. It must be at least 13 characters long.";
    } else {
        // Check if the input contains 'To' indicating a range
        if (strpos($serial_number_input, 'To') !== false) {
            list($start_serial, $end_serial) = explode('To', $serial_number_input);
            $start_serial = trim($start_serial);
            $end_serial = trim($end_serial);

            // Generate the list of serial numbers between start and end
            try {
                $serial_numbers = generateSerialNumbers($start_serial, $end_serial);
            } catch (Exception $e) {
                $message = $e->getMessage(); // Handle error in generating serial numbers
            }
        } else {
            // Otherwise, it's a single serial number
            $serial_numbers[] = $serial_number_input;
        }

        // Step 2: Calculate the expected amount of fuel
        $expected_amount = count($serial_numbers) * 20; // Each serial number represents 20 liters

        // Validate the amount
        if ($amount != $expected_amount) {
            $message = "Invalid amount. The amount of fuel should be " . $expected_amount . " liters for " . count($serial_numbers) . " serial numbers.";
        } else {
            // Step 3: Check if any serial number already exists
            foreach ($serial_numbers as $serial_number) {
                // Check if the serial number exists in the database
                $stmt_check = $conn->prepare("SELECT COUNT(*) FROM add_fuel_tb WHERE serial_number = ?");
                $stmt_check->execute([$serial_number]);
                $count = $stmt_check->fetchColumn();

                if ($count > 0) {
                    // If the serial number already exists, set an error message and stop the process
                    $message = "Error: Serial number $serial_number already exists in the database.";
                    break;
                }
            }

            // Step 4: If no duplicate serial number is found, insert the data
            if ($message === '') {  // Only proceed if no duplicates were found
                $current_timestamp = date('Y-m-d H:i:s'); // Get the current timestamp
                try {
                    $stmt = $conn->prepare('INSERT INTO add_fuel_tb (serial_number, supplier, fuel_type, amount, email, otp_verification, added_at) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $log_stmt = $conn->prepare('INSERT INTO add_fuel_logs_tb (add_id, ec_number, serial_number, supplier, fuel_type, amount, email, otp_verification, added_at) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

                    // Insert each serial number into the database and log the entry
                    foreach ($serial_numbers as $serial_number) {
                        // Insert into add_fuel_tb
                        $stmt->execute([$serial_number, $supplier, $fuel_type, 20, $email, $otp_verification, $current_timestamp]);

                        // Retrieve the add_id of the inserted row
                        $add_id = $conn->lastInsertId();

                        // Insert a log entry into add_fuel_logs_tb
                        $log_stmt->execute([$add_id, $ec_number, $serial_number, $supplier, $fuel_type, 20, $email, $otp_verification, $current_timestamp]);
                    }

                    $message = "Fuel added successfully for all serial numbers."; // Set the success message
                } catch (PDOException $e) {
                    // Catch any error and show the error message
                    $message = "Error: " . $e->getMessage();
                }
            }
        }
    }
}

    

// Fetch all fuel data from the add_fuel_tb
$totalFuelQuery = $conn->query("SELECT * FROM add_fuel_tb");
$fuelData = $totalFuelQuery->fetchAll();

$drawnFuelQuery = $conn->query("SELECT fuel_type, SUM(amount) as drawn FROM fuel_transaction_tb GROUP BY fuel_type");
$drawnFuelData = $drawnFuelQuery->fetchAll();



// Calculate available fuel
$availableFuel = [];
foreach ($fuelData as $fuel) {
    if (!isset($availableFuel[$fuel['fuel_type']])) {
        $availableFuel[$fuel['fuel_type']] = 0;
    }
    $availableFuel[$fuel['fuel_type']] += $fuel['amount'];
}

foreach ($drawnFuelData as $drawn) {
    if (isset($availableFuel[$drawn['fuel_type']])) {
        $availableFuel[$drawn['fuel_type']] -= $drawn['drawn'];
    }
}

$petrolAvailable = $availableFuel['Petrol'] ?? 0;
$dieselAvailable = $availableFuel['Diesel'] ?? 0;
$totalDrawnFuel = array_sum(array_column($drawnFuelData, 'drawn'));

function generateSerialNumbers($start_serial, $end_serial) {
    $serial_numbers = [];
    
    // Extract the numeric part from the serial numbers (using regular expression to capture all digits)
    preg_match('/\d+$/', $start_serial, $start_match); // Match all digits at the end of the start serial
    preg_match('/\d+$/', $end_serial, $end_match); // Match all digits at the end of the end serial
    
    if (empty($start_match) || empty($end_match)) {
        throw new Exception("Invalid serial number format.");
    }

    $start_number = (int)$start_match[0]; // Convert the matched numeric part to integer
    $end_number = (int)$end_match[0]; // Convert the matched numeric part to integer

    // Check that the start number is less than or equal to the end number
    if ($start_number > $end_number) {
        throw new Exception("Start serial number must be less than or equal to end serial number.");
    }

    // Extract the prefix (non-numeric part) from the serial numbers
    $prefix = substr($start_serial, 0, strlen($start_serial) - strlen($start_match[0])); // Get everything before the digits

    // Ensure the prefix of the start and end serials are the same
    $end_prefix = substr($end_serial, 0, strlen($end_serial) - strlen($end_match[0]));
    if ($prefix !== $end_prefix) {
        throw new Exception("Prefixes do not match.");
    }

    // Determine the length of the numeric part for padding
    $number_length = strlen($start_match[0]);

    // Generate serial numbers between start and end (inclusive of both)
    for ($i = $start_number; $i <= $end_number; $i++) {
        // Create the serial number using the prefix and the incremented number
        $serial_numbers[] = $prefix . str_pad($i, $number_length, '0', STR_PAD_LEFT);
    }

    return $serial_numbers;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Fuel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #eef2f3;
            display: flex;
            margin: 0;
            padding: 0;
            min-height: 100vh; /* Ensure full height */
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
            padding: 12px; /* Reduced padding for smaller text */
            text-align: center;
            font-size: 0.9rem; /* Reduced font size */
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar i {
            display: block;
            font-size: 1.3em; /* Reduced icon size */
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
            margin-left: 250px; /* Adjust based on the sidebar width */
            margin-top: 60px; /* Adjust to header height */
            padding: 20px;
            flex: 1;
            width: calc(100% - 250px); /* Adjust width to fill the remaining space */
        }
        form {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 630px;
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
        .alert {
            margin-bottom: 20px;
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

    <div class="main-content">
        <h2 class="text-center">Replenish Fuel</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="serial_number" placeholder="Serial Number (or Range, e.g., PU006B1234512 To PU006B1234600)" required>
            <input type="text" name="supplier" placeholder="Supplier" required>
            <select name="fuel_type">
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
            </select>
            <input type="number" name="amount" placeholder="Amount (Litres)" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="otp_verification" placeholder="OTP Verification" required>
            <button type="submit">Add Fuel</button>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
