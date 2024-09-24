<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN PAGE</title>
    <link rel="stylesheet" href="adminpage.css">
</head>
<body>
    <div id="adminname">
        <header style="color: rgb(255, 132, 16); font-size: 70px; font-family:'Times New Roman', Times, serif">ADMIN</header>
    </div>
    <div class="admin-container">
        <a href="user.php"><button class="btndeco1" role="button"><b>USERS</b></button></a><br><br>
        <a href="alllogs.php"><button class="btndeco2" role="button"><b>ALL LOGS</b></button></a><br><br>
        <a href="Report.php"><button class="btndeco3" role="button"><b>REPORTS</b></button></a><br><br>
    </div>
    <div class="logout-container">
        <a href="logout.php"><button class="logout" role="button"><b>LOGOUT</b></button></a>
    </div>
</body>
</html>
