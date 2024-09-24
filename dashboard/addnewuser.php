<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.html');
    exit();
}

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

$response = ''; // Initialize response variable

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $rf_id = $_POST['rf_id'];
    $register_number = $_POST['register_number'];
    $full_name = $_POST['full_name'];
    $department = $_POST['department'];
    $batch = $_POST['batch'];

    // Check if all required fields are filled
    if (!empty($rf_id) && !empty($register_number) && !empty($full_name) && !empty($department) && !empty($batch)) {
        // Prepare and bind the update statements
        $stmt_update_details = $conn->prepare("UPDATE `Details` SET `REGD`= ?,`NAME`= ?,`DEPARTMENT`= ? ,`BATCH`= ? WHERE `REGD`= ?");
        $stmt_update_details->bind_param("sssss", $register_number, $full_name, $department, $batch, $rf_id);

        // Execute the update statement for `Details` table
        if ($stmt_update_details->execute()) {
            $response = "Record has been updated successfully!!";
        } else {
            $response = "Error updating record in Details: " . $stmt_update_details->error;
        }
        $stmt_update_details->close();

        // Update the `ENTRY_Table` with new name and registration number
        $stmt_update_entry = $conn->prepare("UPDATE `ENTRY_Table` SET `NAME`= ?, `REGD`= ? WHERE `REGD`= ?");
        $stmt_update_entry->bind_param("sss", $full_name, $register_number, $rf_id);

        if (!$stmt_update_entry->execute()) {
            $response = "Error updating record in ENTRY_Table: " . $stmt_update_entry->error;
        }
        $stmt_update_entry->close();
    } else {
        $response = "All fields are required.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD NEW USER PAGE</title>
    <link rel="stylesheet" href="addnewuser.css">
    <script>
    window.onload = function() {
        var response = "<?php echo $response; ?>";
        if (response) {
            // Show the confirmation dialog only if response is not empty
            alert(response);
            if (response.includes("successfully")) {
                // Redirect only if the message includes "successfully"
                window.location.href = 'addnewuser.php';
            }
        }
    }
    </script>
</head>
<body>
  <div id="parent-cont">
    <div id="image_add">
      <img src="images/user-file.png" alt="image">
    </div>
    <div class="container-adduser">
        <div class="login form">
            <header style="color: rgb(255, 132, 16);">Update User</header>
            <form action="addnewuser.php" method="post">
              <input type="text" name="rf_id" placeholder="ID" required>
              <input type="text" name="register_number" placeholder="Register Number" required>
              <input type="text" name="full_name" placeholder="Full Name" required>
              <input type="text" name="department" placeholder="Department" required>
              <input type="text" name="batch" placeholder="Batch" required>
              <input type="submit" class="button" value="ADD">
            </form>
          </div>
    </div> 
  </div>
  <div class="logout-container">
    <a href="user.php"><button class="logout" role="button"><b><--BACK</b></button></a>
</div>  
</body>
</html>
