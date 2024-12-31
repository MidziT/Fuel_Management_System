<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color:#e87f0a;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2rem;
        }
        .system-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .system-card {
            background-color: white;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .system-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        .system-card i {
            font-size: 3rem;
            color: #e87f0a;
            margin-bottom: 10px;
        }
        .system-card h3 {
            font-size: 1.5rem;
            margin: 0 0 10px;
        }
        .system-card a {
            text-decoration: none;
            color: white;
            background-color: #343a40;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .system-card a:hover {
            background-color: #e87f0a;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: white;
            color: grey;
            font-size: small;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/zhrc_logo123.png" alt="ZHRC Logo" style="width: 100px; height: auto;"> <!-- Adjust width and height as needed -->
        <h1>Main Dashboard</h1>
        <p>Select a system to access</p>
    </div>

    <div class="system-container">
        <!-- System 1 -->
        <div class="system-card">
            <i class="bi bi-fuel-pump"></i>
            <h3>Fuel Management System</h3>
            <a href="userlogin.php">Access System</a>
        </div>

        <!-- System 2 -->
        <div class="system-card">
            <i class="bi bi-people"></i>
            <h3>File Tracking System</h3>
            <a href="#">Access System</a>
        </div>

        <!-- System 3 -->
        <div class="system-card">
            <i class="bi bi-wallet"></i>
            <h3>Assets Management System</h3>
            <a href="#">Access System</a>
        </div>

        <!-- System 4 -->
        <div class="system-card">
            <i class="bi bi-box-seam"></i>
            <h3>Document Voice Reader System</h3>
            <a href="voice_app.php">Access System</a>
        </div>

        <!-- System 5 -->
        <div class="system-card">
            <i class="bi bi-graph-up"></i>
            <h3>ICT Support Ticketing System</h3>
            <a href="http://localhost:8501">Access System</a>
        </div>
    </div>


    <footer>
        Powered By Midzi-Tech &copy; <?php echo date('Y'); ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
