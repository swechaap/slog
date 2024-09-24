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
    <title>USER PAGE</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
   <div id="adminname" style="display: flex; justify-content: center; align-items: center; padding-top: 60px;">
        <header style="color: rgb(255, 132, 16); font-size: 70px; font-family: 'Times New Roman', Times, serif;">USER</header>
   </div>
   <div class="admin-container" style="display: flex; flex-direction: column; align-items: center; margin-top: 30px;">
        <a href="addnewuser.php"><button class="btndeco1" role="button"><b>ADD USER</b></button></a><br><br>
        <a href="removeuser.php"><button class="btndeco2" role="button"><b>REMOVE USER</b></button></a><br><br>
        <a href="Details.php"><button class="btndeco3" role="button"><b>ALL USERS</b></button></a><br><br>
   </div>
   <div class="logout-container" style="position: absolute; top: 20px; left: 20px;">
        <a href="adminpage.php"><button class="logout" role="button"><b>&larr; BACK</b></button></a>
   </div>
</body>
</html>
