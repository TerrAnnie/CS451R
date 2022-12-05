<?php

require_once 'UserSession.php';
session_start();
if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
    $session->unsetApplicationPage();
} else {
    $_SESSION = new UserSession();
}

//output error message if user tries to add course already listed add update session tag if time warrants add semester and year options as well
$session->displayHeader("UMKC Opportunities");

$studentID = "";
$currentLevel = "";
$graduatingSemester = "";
$cumulativeGPA = "";
$hoursCompleted = "";
$undergraduateDegree = "";
$currentMajor = "";
$currentMajor_err = "";
$transcript = "";
$readTranscript = "";
$applyFor = "";
$yearChoice="";
$gtaCertification = "";
$readGTACertification = "";
$input = [];
$submission_err = "";
$course_ID = "";

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {

    // Check if student ID is empty


    // Check if current level is selected
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo "<br><br><br><br><br><br><br>";
        $gpa = $_POST["cumulativeGPA"];
        $session->inputs['gpa'] = $gpa;

        if(empty($gpa)){
            $gpa = 0.0;
        }



        $currentLevel = $_POST["currentLevel"];
        $session->inputs['currentLevel'] = $currentLevel;
        $posType = $_POST['posType'];
        $hoursCompleted = $_POST['hoursCompleted'];
        $session->inputs["hoursCompleted"] = $hoursCompleted;
        if($session->isAdmin()){
            $professor_ID = $session->getID();
        }
        else{
            $professor_ID = $session->profIDAdmin;
        }

        if(!empty($session->editCourseID)){//in edit mode
            $course_ID = $session->editCourseID;
        }

        if(!empty($session->listingUpdate)){

            $sql = "select course_id from listing where listing_id = '$session->listingUpdate'";
            $result = mysqli_query($session->getLink(),$sql);

            $row = mysqli_fetch_array($result);
            $course_ID = $row['course_id'];
        }

        if($session->checkErrors($course_ID,$professor_ID)){
            $submission_err = "Please choose a correct Position type for this course";
        }

        if(empty($_POST["semesterYear"])){//check to see if submission for semester/year is done
            $submission_err = "Please choose a semester/year";
        }


        if(empty($submission_err)){ //if there are no errors process the listing

            $timeOfYearList= $_POST['semesterYear'];
            $session->processAddListing($gpa,$professor_ID,$currentLevel,$posType,$hoursCompleted,$course_ID,$timeOfYearList);
        }

        else {
            echo '<script> alert(" '.$submission_err.'") </script>';



        }


    }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add/Update Listing </title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="style.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <style>
        .login{
            margin: auto;
            color: white;
            padding: 10px;
            border: none;

            width: 40vw;
            height: 50vh;
            background-color: #0173bc;
        }
        input[type=text], select, number {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;

        }
        input[type=number] {
            width: 100%;
            padding: 12px 20px;

            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type=submit] {
            width: 100%;
            background-color: white;
            color: black;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body style = "background-color: #f0f0f0; font-family: sans-serif;">
<br>
<br>
<br>
<br>
<br>
<br>

<form action = "" method = "post"style= "padding-left: 40px; padding-right: 40px; padding-top: 10px; ">
    <?php
    $count = 0;
    $course_ID = "";
    $professorID = "";
    if(!empty($session->editCourseID)){//if not empty we need to edit/add this course
        $session->addCourseChoice();

    }

    if(!empty($session->listingUpdate)){
        $session-> UpdateCourseChoice();
    }

    ?>

    <br>
    <div style="display: table-cell;" ><label>Current Level </label><select  name="currentLevel" required>
            <option disabled selected value> -- select an option -- </option>
            <option value="0">Undergrad</option>
            <option value="1">MS</option>
            <option value="2">PhD</option>
        </select></div>



    <div style="display: table-cell; padding-left: 20px;"><label>Position Type:</label><select name="posType" required>
            <option disabled selected value> -- select an option -- </option>
            <option value="Grader">Grader</option>
            <option value="GTA">GTA</option>
            <option value="Lab instructor">Lab Instructor</option>

        </select></div>


    <input type = "submit" name ="continue" value = "Submit";>
</form>
</div>

</body>



