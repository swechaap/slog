<?php
$hostname = "localhost";
$username = "slogadmin";
$password = "password";
$database = "slog";

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all RFIDs with missing OUT_Time
$stmt = $conn->prepare("SELECT RF_ID FROM Details WHERE REGD = (SELECT REGD FROM ENTRY_Table WHERE OUT_Time IS NULL)");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rfid = $row["RF_ID"];

        // Simulate URL hit with the RFID
        $url = "http://yourdomain.com/sendid.php?sensor1=" . urlencode($rfid);
        $response = file_get_contents($url);
    }
} else {
    echo "All entries have OUT_Time recorded.";
}

$stmt->close();
$conn->close();
?>
