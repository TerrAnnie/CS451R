<?php

require_once 'UserSession.php';

session_start();

$email_address = "";
$password = "";
$email_error = false;
$password_error =  false;
$login_err = "";

if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
} else {
    $session = new UserSession();
}

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    $session->checkLoginInputs($_POST['email'], $_POST['password']);
    $_SESSION['session'] = $session;
    header('location: index.php');
}

$session->displayHeader('Login');
?>
<br>
<br>
<br>
<br>
<br>
<br>
<div style = "text-align: center"> <a href = "https://www.umkc.edu/">  <img src="UMKC_Logo.png" alt="Umkc Logo" width = 200px height = 100px>  </a>
    <br> <h1 style = "font-family: sans-serif;"> Welcome to UMKC's Grader Portal</h1>
</div>


<div class = "login" style="height: 53vh; border: none;"> <h2 style = "text-align: center;"> Login  </h2> <hr class = solid>
    <form method = "post" style = "padding-left: 40px; padding-right: 40px; padding-top: 10px; action = "">
    <label for="email">UMKC E-mail</label>
    <input type="text" id="email" name="email" placeholder="e.g username@umsystem.edu">
    <br>

    <label for="password">UMKC Password</label>
    <input type="password" id="password" name="password" placeholder="e.g UMKC Password">
    <br>
    <br>
    <input type = "submit" style="width: 100%;" name = "submit" value = "Submit">
    <br>

    <a href = "index.php" style = "color: white; font-size: 20px;" ><p style = "text-align: center;"> Return to Listings Page </p></a>



    </form>


</div>


</body>
</html>
