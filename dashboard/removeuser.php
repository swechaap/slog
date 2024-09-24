<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit();
}

// Establish database connection
$servername = "localhost";
$username = "slogadmin";
$password = "password";
$database = "slog";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = '';
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve Regd from the form
    if(isset($_POST['Regd'])) {
        $Regd = $conn->real_escape_string($_POST['Regd']); // Escaping to prevent SQL injection

        // SQL query to delete row from Details table where REGD matches
        $sql = "DELETE FROM Details WHERE REGD='$Regd'";

        if ($conn->query($sql) === TRUE) {
            $response = "Record removed successfully!";
        } else {
            $response = "Error: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REMOVE USER PAGE</title>
    <link rel="stylesheet" href="removeuser.css">
    <script>
    window.onload = function() {
        var response = "<?php echo $response; ?>";
        if (response) {
            // Show the confirmation dialog only if response is not empty
            if (confirm(response)) {
                window.location.href = 'removeuser.php';
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
            <header style="color: rgb(255, 132, 16);">Remove User</header>
            <form action="removeuser.php" method="post">
              <input type="text" name="Regd" placeholder="REGD" required>
              <input type="submit" class="button" value="REMOVE">
            </form>
          </div>
    </div> 
  </div> 
  <div class="logout-container">
    <a href="user.php"><button class="logout" role="button"><b><--BACK</b></button></a>
  </div>  
</body>
</html>
