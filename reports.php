<?php
require 'db.php'; // Include database connection

// Start session for managing user sessions
session_start();

// Redirect if session is not set (i.e., user is not logged in)
if (!isset($_SESSION['ec_number'])) {
    header('Location: userlogin.php');
    exit();
}

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
$username = $user['username']; // Access the username from the database
$name = $user['name']; // Access the name from the database
$surname = $user['surname']; // Access the surname from the database
$email = $user['email']; // Access the email from the database

// Fetching fuel data for analysis
// Define time periods for daily, weekly, monthly, quarterly, yearly
$timePeriods = [
    'daily' => 'INTERVAL 1 DAY',
    'weekly' => 'INTERVAL 1 WEEK',
    'monthly' => 'INTERVAL 1 MONTH',
    'quarterly' => 'INTERVAL 3 MONTH',
    'yearly' => 'INTERVAL 1 YEAR'
];

$data = [];

// Loop through time periods and fetch the total amount of fuel added, drawn, and service data in each period
foreach ($timePeriods as $periodName => $interval) {
    // Query for added fuel
    $addFuelQuery = $conn->prepare("
        SELECT fuel_type, SUM(amount) as total_added
        FROM add_fuel_tb
        WHERE added_at >= NOW() - $interval
        GROUP BY fuel_type
    ");
    $addFuelQuery->execute();
    $addedData = $addFuelQuery->fetchAll(PDO::FETCH_ASSOC);

    // Query for drawn fuel
    $drawnFuelQuery = $conn->prepare("
        SELECT fuel_type, SUM(amount) as total_drawn
        FROM fuel_transaction_tb
        WHERE transaction_time >= NOW() - $interval
        GROUP BY fuel_type
    ");
    $drawnFuelQuery->execute();
    $drawnData = $drawnFuelQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch service data for analysis
    $serviceQuery = $conn->prepare("
        SELECT fuel_type, SUM(amount) as total_service
        FROM condition_service_tb
        WHERE transaction_timestamp >= NOW() - $interval
        GROUP BY fuel_type
    ");
    $serviceQuery->execute();
    $serviceData = $serviceQuery->fetchAll(PDO::FETCH_ASSOC);

    // Include this service data in your $data array
    $data[$periodName] = [
        'added' => $addedData,
        'drawn' => $drawnData,
        'service' => $serviceData
    ];
}

// Fetch data for the table
$tableQuery = $conn->prepare("
    SELECT 
        issued_to AS name, 
        email_address AS email, 
        fuel_type, 
        SUM(amount) AS total_fuel
    FROM fuel_transaction_tb
    WHERE transaction_time >= NOW() - INTERVAL 1 MONTH
    GROUP BY issued_to, email_address, fuel_type

    UNION ALL

    SELECT 
        issued_to AS name, 
        email, 
        fuel_type, 
        SUM(amount) AS total_fuel
    FROM condition_service_tb
    WHERE transaction_timestamp >= NOW() - INTERVAL 1 MONTH
    GROUP BY issued_to, email, fuel_type
");


$tableQuery->execute();
$tableData = $tableQuery->fetchAll(PDO::FETCH_ASSOC);

// Pass table data to JavaScript
?>
<script>
    const tableData = <?php echo json_encode($tableData); ?>;
</script>



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
    <div class="container mt-5">
        <h1 class="text-center">Fuel Management Analytics</h1>

        <!-- Dropdown for Time Period Selection -->
        <div class="mb-4 text-center">
            <label for="timePeriod" class="form-label">Select Time Period:</label>
            <select id="timePeriod" class="form-select w-auto d-inline-block">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
            
            <button id="printReportButton" class="btn btn-primary">Print PDF Report</button>


        </div>

       <!-- Fuel Data Charts -->
        <div class="row">
            <div class="col-md-4">
                <h5 class="text-center">Fuel Added</h5>
                <canvas id="addedFuelChart"></canvas>
            </div>
            <div class="col-md-4">
                <h5 class="text-center">Fuel Drawn</h5>
                <canvas id="drawnFuelChart"></canvas>
            </div>
            <div class="col-md-4">
                <h5 class="text-center">Condition of Service</h5>
                <canvas id="serviceChart"></canvas>
            </div>
        </div>

        <h2 class="text-center">Fuel Transaction & Condition of Service Overview</h2>
        <div class="table-responsive">
            <table id="fuelDataTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Fuel Type</th>
                        <th>Total Fuel (Liters)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- PHP/JavaScript will populate this section -->
                </tbody>
            </table>
        </div>


    </div>
    
</div>

<footer style="width: 100%; background-color: white; color: grey; text-align: center; padding: 10px; position: fixed; bottom: 0; left: 0; font-size: x-small;">
    Powered By Midzi-Tech
</footer>

<!-- Include Bootstrap JS (Only Once) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    const data = <?php echo json_encode($data); ?>;

    let addedFuelChart, drawnFuelChart, serviceChart;

    function renderCharts(period) {
        const normalize = str => str.trim().toLowerCase();
        const labels = data[period].added.map(item => normalize(item.fuel_type));

        const addedData = labels.map(label => {
            const match = data[period].added.find(item => normalize(item.fuel_type) === label);
            return match ? match.total_added : 0;
        });

        const drawnData = labels.map(label => {
            const match = data[period].drawn.find(item => normalize(item.fuel_type) === label);
            return match ? match.total_drawn : 0;
        });

        const serviceData = labels.map(label => {
            const match = data[period].service.find(item => normalize(item.fuel_type) === label);
            return match ? match.total_service : 0;
        });

        console.log('Labels:', labels);
        console.log('Added Data:', addedData);
        console.log('Drawn Data:', drawnData);
        console.log('Service Data:', serviceData);

        if (addedFuelChart) addedFuelChart.destroy();
        if (drawnFuelChart) drawnFuelChart.destroy();
        if (serviceChart) serviceChart.destroy();

        const addedFuelChartCtx = document.getElementById('addedFuelChart')?.getContext('2d');
        const drawnFuelChartCtx = document.getElementById('drawnFuelChart')?.getContext('2d');
        const serviceChartCtx = document.getElementById('serviceChart')?.getContext('2d');

        if (!addedFuelChartCtx || !drawnFuelChartCtx || !serviceChartCtx) {
            console.error('Canvas context missing!');
            return;
        }

        addedFuelChart = new Chart(addedFuelChartCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Added Fuel (Liters)',
                    data: addedData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        drawnFuelChart = new Chart(drawnFuelChartCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Drawn Fuel (Liters)',
                    data: drawnData,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        serviceChart = new Chart(serviceChartCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Condition of Service (Liters)',
                    data: serviceData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });





    }

        document.getElementById('timePeriod').addEventListener('change', function() {
            renderCharts(this.value);
        });

        renderCharts('daily');
   // Populate the table with data
   document.getElementById('printReportButton').addEventListener('click', function() {
    const timePeriod = document.getElementById('timePeriod').value; // Get selected time period
    window.location.href = `generate_report.php?period=${timePeriod}`;
    });

   const tableBody = document.querySelector('#fuelDataTable tbody');

    tableData.forEach(data => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${data.name}</td>
            <td>${data.email}</td>
            <td>${data.fuel_type}</td>
            <td>${data.total_fuel}</td>
        `;
   

        tableBody.appendChild(row);
    });  
    
    $(document).ready(function() {
        var table = $('#fuelDataTable').DataTable({
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


</script>>



</body>
</html>
