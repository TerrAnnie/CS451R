<?php

require_once 'UserSession.php';
session_start();
if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
    $session->unsetApplicationPage();
} else {
    $_SESSION = new UserSession();
}
$session->displayHeader("UMKC Opportunities");
//Add extra column for professor teaching and figure out how to do that
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel='stylesheet' href='style.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        #submitTable{
            font-family: sans-serif;
            border-collapse: collapse;
            width: 80%;
        }
        #submitTable th, #submitTable td{
            border: 1px solid #ddd;
            padding: 15px;

        }
        #submitTable tr:nth-child(even){background-color: #FCFCF9;}

        #submitTable tr:hover {background-color: #ddd;}

        #submitTable th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #0173bc;
            color: white;
        }
        #submitTable td {
            text-align: center;
        }
        input[type=submit]{
            background: none;
            border: none;
            padding: 0
            /*optional*/
            font-family: arial, sans-serif;
            /*input has OS specific font-family*/
            color: #069;
            text-decoration: underline;
            cursor: pointer;
        }
        .btn {
            background-color: #0173bc;
            border: none;
            color: white;
            padding: 12px 16px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose A Course</title>
</head>
<body style="background-color: #f0f0f0;">
<br><br><br><br><br><br><br><br>
<h1 style="text-align: center; font-family: sans-serif;">Choose a Listing to Manage</h1>
<table id="submitTable" style = "margin: auto;">
    <tr>
        <th>Course Name</th>
        <?php if($session-> isManagingAdmin()){
            echo "<th> Professor </th>";
        } ?>
        <th>Update/Edit Position</th>
    </tr>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(isset($_POST["update"]) and $session->isAdmin()){
            $session->setEditCourseID($_POST['update']);
            header("location: Professor_Add_Listing.php");
        }
        if(isset($_POST["update"]) and $session->isManagingAdmin()){
            $val = $_POST['update'];
            $lastSpace = strpos($val, " ");
            $professor_id= substr ($val, $lastSpace + 1);
            $editCourseID = substr ($val, 0, $lastSpace);
            $session->setEditCourseID($editCourseID, $professor_id);

            header("location: Professor_Add_Listing.php");
        }
    }
    if($session-> isManagingAdmin()){
        $session-> adminClassList();
    }
    else {
        $session->profClassList();
    }
    ?>
</table>
</body>
</html>
