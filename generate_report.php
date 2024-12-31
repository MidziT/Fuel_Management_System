<?php
require 'db.php';
require 'lib/fpdf.php'; // Include FPDF library

// Get the selected time period from the URL
$period = $_GET['period'] ?? 'daily';

// Define the time interval based on the selected period
$intervals = [
    'daily' => 'INTERVAL 1 DAY',
    'weekly' => 'INTERVAL 1 WEEK',
    'monthly' => 'INTERVAL 1 MONTH',
    'quarterly' => 'INTERVAL 3 MONTH',
    'yearly' => 'INTERVAL 1 YEAR',
];

$interval = $intervals[$period] ?? 'INTERVAL 1 DAY';

// Fetch data from the database
// Total fuel added
$addQuery = $conn->prepare("SELECT SUM(amount) AS total_added FROM add_fuel_tb WHERE added_at >= NOW() - $interval");
$addQuery->execute();
$addedFuel = $addQuery->fetchColumn();

// Total fuel dispensed
$dispensedQuery = $conn->prepare("SELECT SUM(amount) AS total_dispensed FROM fuel_transaction_tb WHERE transaction_time >= NOW() - $interval");
$dispensedQuery->execute();
$dispensedFuel = $dispensedQuery->fetchColumn();

// Total fuel dispensed as condition of service
$serviceQuery = $conn->prepare("SELECT SUM(amount) AS total_service FROM condition_service_tb WHERE transaction_timestamp >= NOW() - $interval");
$serviceQuery->execute();
$serviceFuel = $serviceQuery->fetchColumn();

// Calculate the balance
$balance = $addedFuel - ($dispensedFuel + $serviceFuel);

// Create the PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$pdf->Cell(0, 10, 'Fuel Management Report', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Time Period:', 0, 0);
$pdf->Cell(0, 10, ucfirst($period), 0, 1);

$pdf->Cell(50, 10, 'Total Fuel Added:', 0, 0);
$pdf->Cell(0, 10, number_format($addedFuel, 2) . ' Liters', 0, 1);

$pdf->Cell(50, 10, 'Total Fuel Dispensed:', 0, 0);
$pdf->Cell(0, 10, number_format($dispensedFuel, 2) . ' Liters', 0, 1);

$pdf->Cell(50, 10, 'Condition of Service:', 0, 0);
$pdf->Cell(0, 10, number_format($serviceFuel, 2) . ' Liters', 0, 1);

$pdf->Cell(50, 10, 'Balance:', 0, 0);
$pdf->Cell(0, 10, number_format($balance, 2) . ' Liters', 0, 1);

$pdf->Output('D', 'Fuel_Report.pdf'); // Prompt user to download the PDF
exit();

?>
