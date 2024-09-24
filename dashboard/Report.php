<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.html');
    exit();
}

// Database connection
$servername = "localhost";
$username = "slogadmin";
$password = "password";
$database = "slog";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct filter options
$departments = $conn->query("SELECT DISTINCT DEPARTMENT FROM Details")->fetch_all(MYSQLI_ASSOC);
$batches = $conn->query("SELECT DISTINCT BATCH FROM Details")->fetch_all(MYSQLI_ASSOC);

// Get today's date
$today = date('Y-m-d');

// Get the earliest IN_Time
$earliest_in_time_query = "SELECT MIN(IN_Time) AS earliest_in_time FROM ENTRY_Table";
$earliest_in_time_result = $conn->query($earliest_in_time_query);
$earliest_in_time_row = $earliest_in_time_result->fetch_assoc();
$earliest_in_time = $earliest_in_time_row['earliest_in_time'] ?? $today;

// Build the SQL query with filtering
$filters = [];
if (!empty($_POST['name'])) {
    $filters[] = "d.NAME LIKE '%" . $conn->real_escape_string($_POST['name']) . "%'";
}
if (!empty($_POST['department'])) {
    $filters[] = "d.DEPARTMENT = '" . $conn->real_escape_string($_POST['department']) . "'";
}
if (!empty($_POST['batch'])) {
    $filters[] = "d.BATCH = '" . $conn->real_escape_string($_POST['batch']) . "'";
}
if (!empty($_POST['in_time'])) {
    $filters[] = "e.IN_Time >= '" . $conn->real_escape_string($_POST['in_time']) . "'";
}
if (!empty($_POST['out_time'])) {
    $filters[] = "e.OUT_Time <= '" . $conn->real_escape_string($_POST['out_time']) . "'";
}

// If no filters are set, fetch all data
$whereClause = count($filters) > 0 ? 'WHERE ' . implode(' AND ', $filters) : '';

$sql = "
SELECT
    d.NAME AS Student_Name,
    d.REGD AS Register_Number,
    d.DEPARTMENT,
    d.BATCH,
    e.IN_Time,
    e.OUT_Time
FROM
    ENTRY_Table e
JOIN
    Details d ON e.NAME = d.NAME
$whereClause
ORDER BY
    e.IN_Time DESC";

$result = $conn->query($sql);

// Handle CSV download
if (isset($_POST['download_csv'])) {
    // Prepare the CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=visitor_report.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Name', 'Register Number', 'Department', 'Batch', 'In Time', 'Out Time'));

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        *{
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Poppins', sans-serif;
        }
        body{
          min-height: 100vh;
          width: 100%;
          background: #e6e6e6;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        form {
            margin: 20px auto;
            text-align: center;
        }
        label {
            margin-right: 10px;
        }
        input, select, button {
            padding: 5px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .logout-container {
            text-align: left;
            margin: 20px;
        }
        .logout {
            background-color: #c00000;
            border-radius: 100px;
            color: rgb(95, 2, 2);
            cursor: pointer;
            padding: 10px 30px;
            text-align: center;
            text-decoration: none;
            transition: all 250ms;
            border: 0;
            font-size: 20px;
            user-select: none;
            position: relative;
            width: 150px;
        }
        .logout:hover{
            background: #ff1c1c;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <a href="adminpage.php"><button class="logout"><b><-- BACK</b></button></a>
    </div>

    <h1>VISITOR REPORT</h1>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Search for names..">

        <label for="department">Department:</label>
        <select id="department" name="department">
            <option value="">All Departments</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept['DEPARTMENT']) ?>">
                    <?= htmlspecialchars($dept['DEPARTMENT']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="batch">Batch:</label>
        <select id="batch" name="batch">
            <option value="">All Batches</option>
            <?php foreach ($batches as $batch): ?>
                <option value="<?= htmlspecialchars($batch['BATCH']) ?>">
                    <?= htmlspecialchars($batch['BATCH']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="in_time">In Time:</label>
        <input type="date" id="in_time" name="in_time" value="<?= htmlspecialchars($earliest_in_time) ?>">

        <label for="out_time">Out Time:</label>
        <input type="date" id="out_time" name="out_time" value="<?= htmlspecialchars($today) ?>">

        <button type="submit" name="filter">Filter</button>
        <button type="submit" name="download_csv">Download CSV</button>
    </form>

    <table id="dataTable">
        <tr>
            <th>Name</th>
            <th>Register Number</th>
            <th>Department</th>
            <th>Batch</th>
            <th>In Time</th>
            <th>Out Time</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Student_Name']) ?></td>
                    <td><?= htmlspecialchars($row['Register_Number']) ?></td>
                    <td><?= htmlspecialchars($row['DEPARTMENT']) ?></td>
                    <td><?= htmlspecialchars($row['BATCH']) ?></td>
                    <td><?= htmlspecialchars($row['IN_Time']) ?></td>
                    <td><?= htmlspecialchars($row['OUT_Time']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No results found</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
