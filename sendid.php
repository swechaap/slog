<?php
$hostname = "localhost";
$username = "root";
$password = "Swecha@23";
$database = "slog";

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if data is available in the request
if (isset($_REQUEST['sensor1'])) {
    $sensor1 = $_REQUEST['sensor1'];

    // Insert data into the Rfid table using a prepared statement
    $stmt_insert = $conn->prepare("INSERT INTO Rfid (RF_ID) VALUES (?)");
    $stmt_insert->bind_param("s", $sensor1);
    if (!$stmt_insert->execute()) {
        echo "Error: " . $stmt_insert->error;
    }
    $stmt_insert->close();
    $stmt_count1 = $conn->prepare("SELECT Rfid.Sno AS id_count FROM Rfid WHERE Rfid.RF_ID = ? ");
    $stmt_count1->bind_param("s", $sensor1);
    $stmt_count1->execute();
    $result_count = $stmt_count1->get_result();
    if ($result_count->num_rows > 0) {
        $row_count = $result_count->fetch_assoc();
        $id_count = $row_count["id_count"];
    }
    
    // Check if the RF_ID already exists in the Details table
    $stmt_check = $conn->prepare("SELECT RF_ID FROM Details WHERE RF_ID = ?");
    $stmt_check->bind_param("s", $sensor1);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        // If RF_ID does not exist in Details, insert a new record with default values
        $stmt_insert_details = $conn->prepare("INSERT INTO Details (RF_ID, REGD, NAME, DEPARTMENT, BATCH) VALUES (?, ?, 'x', 'x', 'x')");
        $stmt_insert_details->bind_param("ss", $sensor1, $id_count);
        if (!$stmt_insert_details->execute()) {
            echo "Error: " . $stmt_insert_details->error;
        }
        $stmt_insert_details->close();
    }
    $stmt_count1->close();
    $stmt_check->close();

    // Retrieve name and REGD from the Details table based on RF_ID using a prepared statement
    $stmt_details = $conn->prepare("SELECT NAME, REGD FROM Details WHERE RF_ID = ?");
    $stmt_details->bind_param("s", $sensor1);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();

    if ($result_details->num_rows > 0) {
        $row_details = $result_details->fetch_assoc();
        $name = $row_details["NAME"];
        $regd = $row_details["REGD"];

        // Count occurrences of the ID in the Rfid table
        $stmt_count = $conn->prepare("SELECT COUNT(*) AS id_count FROM Rfid WHERE RF_ID = ?");
        $stmt_count->bind_param("s", $sensor1);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $status = "";

        if ($result_count->num_rows > 0) {
            $row_count = $result_count->fetch_assoc();
            $id_count = $row_count["id_count"];
            // Determine status based on whether the count is odd or even
            $status = ($id_count % 2 == 0) ? "VISIT AGAIN !!!" : "WELCOME TO SLC";

            if ($name != "") {
                // Update ENTRY_Table with name and IN_Time/OUT_Time based on status
                if ($id_count % 2 == 0) {
                    $stmt_update = $conn->prepare("UPDATE ENTRY_Table SET OUT_Time = CURRENT_TIMESTAMP() WHERE OUT_Time IS NULL AND NAME = ? AND REGD = ?");
                    $stmt_update->bind_param("ss", $name, $regd);
                } else {
                    $stmt_update = $conn->prepare("INSERT INTO ENTRY_Table (NAME, REGD, IN_Time) VALUES (?, ?, CURRENT_TIMESTAMP())");
                    $stmt_update->bind_param("ss", $name, $regd);
                }
                if ($name != "x") {
                    echo "Regd: " . $regd . "<hr>";
                    echo $status;
                } else if ($name == "x" && $id_count % 2 == 0) {
                    echo "Invalid ID: " . $regd . "<hr>";
                    echo "O-GET Registered";
                } else if ($name == "x" && $id_count % 2 != 0) {
                    echo "Invalid ID: " . $regd . "<hr>";
                    echo "I-GET Registered";
                }
                if (!$stmt_update->execute()) {
                    echo "Error updating ENTRY_Table: " . $stmt_update->error;
                }
                $stmt_update->close();
            }
        }
    }
    $stmt_details->close();
}

$conn->close();
?>
