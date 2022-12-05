//this is unused in the project but inserts new users into the database with a hashed password

<?php

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$sql = "INSERT INTO 'user' (email_address,  password, role) VALUES (?, ?, ?)";
if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "sss", $param_email, $param_password, $param_isAdmin);
    $param_email ='tarngp@umkc.edu';
    $param_password = password_hash("password1", PASSWORD_DEFAULT);
    $param_first_name = 'Tony';
    $param_last_name = 'Russell';
    $param_isAdmin = 0;

    if(mysqli_stmt_execute($stmt)){
        echo "success";
    } else{
        echo "failed";
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);