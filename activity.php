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

// Display user-specific data on the dashboard
$username = $user['username']; // Access the username from the database
$name = $user['name']; // Access the name from the database
$surname = $user['surname']; // Access the surname from the database
$email = $user['email']; // Access the email from the database
?>

<?php
// Fetch all fuel data from add_fuel_tb
$totalFuelQuery = $conn->query("SELECT * FROM add_fuel_tb");
$fuelData = $totalFuelQuery->fetchAll();

$drawnFuelQuery = $conn->query("SELECT fuel_type, SUM(amount) as drawn FROM fuel_transaction_tb GROUP BY fuel_type");
$drawnFuelData = $drawnFuelQuery->fetchAll();

// Calculate available fuel
$availableFuel = [];
foreach ($fuelData as $fuel) {
    $availableFuel[$fuel['fuel_type']] = ($availableFuel[$fuel['fuel_type']] ?? 0) + $fuel['amount'];
}

foreach ($drawnFuelData as $drawn) {
    if (isset($availableFuel[$drawn['fuel_type']])) {
        $availableFuel[$drawn['fuel_type']] -= $drawn['drawn'];
    }
}

$petrolAvailable = $availableFuel['Petrol'] ?? 0;
$dieselAvailable = $availableFuel['Diesel'] ?? 0;
$totalDrawnFuel = array_sum(array_column($drawnFuelData, 'drawn'));

// Fetch fuel logs from add_fuel_logs_tb
$fuelLogsQuery = $conn->query("SELECT * FROM add_fuel_logs_tb ORDER BY added_at DESC");
$fuelLogs = $fuelLogsQuery->fetchAll();

// Fetch condition service data from condition_service_tb
$conditionServiceQuery = $conn->query("SELECT * FROM condition_service_tb ORDER BY transaction_timestamp DESC");
$conditionServiceData = $conditionServiceQuery->fetchAll();

// Fetch data from fuel_transaction_tb
$fuelTransactionQuery = $conn->query("SELECT serial_number, fuel_type, amount, car_registration_number, issued_to, purpose_of_issue, email_address, ec_number, transaction_time, destination, kilometres FROM fuel_transaction_tb ORDER BY transaction_time DESC");
$fuelTransactionData = $fuelTransactionQuery->fetchAll();

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
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
        }

        .flex-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .table-scroll {
            max-height: 300px;
            overflow-y: auto;
            font-size: 0.85rem;
        }

        th, td {
            font-size: 0.85rem;
        }

        th {
            background-color: black;
            color: #e87f0a;
            cursor: pointer;
        }

        #searchBar {
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chart-container {
            flex: 1;
            max-width: 400px;
        }

        .container {
            flex: 2;
        }

        input:disabled {
            background-color: #f4f4f4;
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

<!-- Account Details Modal -->
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

<!-- Logout Confirmation Modal -->
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
    <!-- Fuel Levels and Alerts -->
    <?php if ($petrolAvailable <= 1000 || $dieselAvailable <= 1000): ?>
        <div class="alert alert-warning" role="alert">
            Attention: Fuel levels are low! Please check.
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-4">
            <div class="alert alert-info" role="alert">
                Available Petrol: <?php echo $petrolAvailable; ?> Liters
            </div>
        </div>
        <div class="col-4">
            <div class="alert alert-info" role="alert">
                Available Diesel: <?php echo $dieselAvailable; ?> Liters
            </div>
        </div>
        <div class="col-4">
            <div class="alert alert-info" role="alert">
                Total Fuel Drawn: <?php echo $totalDrawnFuel; ?> Liters
            </div>
        </div>
        
    </div>

    <!-- Fuel Added Logs Table -->
    <div class="container">
        <h4 class="text-primary text-black">Fuel Added Logs</h4>

        <input type="text" id="searchBar" placeholder="Search...">
        <div class="table-scroll">
            <table class="table table-bordered" id="fuelLogsTable">
                <thead>
                    <tr>
                        <th>EC Number</th>
                        <th>Serial Number</th>
                        <th>Fuel Type</th>
                        <th>Amount</th>
                        <th>Email</th>
                        <th>OTP Verification</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fuelLogs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['ec_number']); ?></td>
                            <td><?php echo htmlspecialchars($log['serial_number']); ?></td>
                            <td><?php echo htmlspecialchars($log['fuel_type']); ?></td>
                            <td><?php echo htmlspecialchars($log['amount']); ?></td>
                            <td><?php echo htmlspecialchars($log['email']); ?></td>
                            <td><?php echo htmlspecialchars($log['otp_verification']); ?></td>
                            <td><?php echo htmlspecialchars($log['added_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Condition Service Table -->
    <div class="container mt-5">
        <h4 class="text-primary text-black">Condition Service Logs</h4>
        
        <div class="table-scroll">
            <table class="table table-bordered" id="conditionServiceTable">
                <thead>
                    <tr>
                        <th>EC Number</th>
                        <th>Serial Number</th>
                        <th>Fuel Type</th>
                        <th>Amount</th>
                        <th>Issued To</th>
                        <th>Email</th>
                        <th>OTP Verification</th>
                        <th>Dispensed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conditionServiceData as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['ec_number']); ?></td>
                            <td><?php echo htmlspecialchars($service['serial_number']); ?></td>
                            <td><?php echo htmlspecialchars($service['fuel_type']); ?></td>
                            <td><?php echo htmlspecialchars($service['amount']); ?></td>
                            <td><?php echo htmlspecialchars($service['issued_to']); ?></td>
                            <td><?php echo htmlspecialchars($service['email']); ?></td>
                            <td><?php echo htmlspecialchars($service['otp_verification']); ?></td>
                            <td><?php echo htmlspecialchars($service['transaction_timestamp']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fuel Transaction Logs Table -->
    <div class="container mt-5">
        <h4 class="text-primary text-black">Fuel Transaction Logs</h4>

        <div class="table-scroll">
            <table class="table table-bordered" id="fuelTransactionTable">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Fuel Type</th>
                        <th>Amount</th>
                        <th>Car Registration Number</th>
                        <th>Issued To</th>
                        <th>Purpose of Issue</th>
                        <th>Email Address</th>
                        <th>EC Number</th>
                        <th>Dispensed At</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fuelTransactionData as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['serial_number']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['fuel_type']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['car_registration_number']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['issued_to']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['purpose_of_issue']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['email_address']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['ec_number']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['transaction_time']); ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    
</div>

<footer style="width: 100%; background-color: white; color: grey; text-align: center; padding: 10px; position: fixed; bottom: 0; left: 0; font-size: x-small;">
    Powered By Midzi-Tech
</footer>

<!-- Bootstrap, jQuery, DataTables, and other JavaScript includes -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#fuelLogsTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 20, 50],
            "searching": true,
            "paging": true,
            "info": true
        });

        $('#searchBar').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    $(document).ready(function() {
        var table = $('#conditionServiceTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 20, 50],
            "searching": true,
            "paging": true,
            "info": true
        });

        $('#searchBar').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    $(document).ready(function() {
        var table = $('#fuelTransactionTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 20, 50],
            "searching": true,
            "paging": true,
            "info": true
        });

        $('#searchBar').on('keyup', function() {
            table.search(this.value).draw();
        });
    });


    
   
</script>

</body>
</html>


