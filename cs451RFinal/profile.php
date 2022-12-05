<?php

require_once 'UserSession.php';
session_start();
if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
    $session->unsetApplicationPage();
} else {
    $_SESSION = new UserSession();
    header('location: index.php');
}
$session->displayHeader("Profile");
$studentID = "";
$currentLevel = "";
$graduatingSemester = "";
$cumulativeGPA = "";
$hoursCompleted = 0;
$undergraduateDegree = "";
$currentMajor = "";
$currentMajor_err = "";
$transcript = "";
$readTranscript = "";
$applyFor = "";

$gtaCertification = "";
$readGTACertification = "";

$submission_err = "";
$input=[];

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {

    // Check if student ID is empty


    // Check if current level is selected
    echo '<br><br><br>';
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $currentMajor = $_POST['currentMajor'];
        $input['currentMajor'] = $currentMajor;
        $currentLevel = $_POST["currentLevel"];
        $input['currentLevel'] = $currentLevel;
        $currentHours = $_POST['hoursCompleted'];
        $input['currentHours'] = $currentHours;
        $transcript = $_POST["transcript"];
        $transcript = stripslashes($transcript);
        $input['transcript'] = $transcript;
        $gpa = $_POST["cumulativeGPA"];
        $input['gpa'] = $gpa;
        $resume = $_POST['resume'];
        $resume = stripslashes($resume);
        $input['resume'] = $resume;
        $GTA = $_POST["GTA"];
        $input ['GTA'] = $GTA;
        if(empty($currentHours)){
            echo"<script>alert('no input')</script>";
        }

        // Check if graduating semester is selected
        if (empty(trim($_POST["graduatingSemester"]))) {
            $submission_err = "Please enter your expected graduation date";
        } else {
            $graduatingSemester = trim($_POST["graduatingSemester"]);
            $input['grad_semester'] = $graduatingSemester;
        }
        if (empty(trim($_POST["transcript"]))) {
            $submission_err = "Please link your transcript";
        } else {
            $transcript= trim($_POST["transcript"]);
            $input['transcript'] = $transcript;
        }
        if (empty(trim($_POST["resume"]))) {
            $submission_err = "Please link your resume";
        } else {
            $resume= trim($_POST["resume"]);
            $input['resume'] = $resume;
        }




        if($_POST["International"] == 1 and empty(trim($_POST['GTA']))){
            $submission_err = "Please upload GTA Certification";
        }

        // Check if major is selected


        if (empty($submission_err)) {

            if(empty($gpa)){
                $gpa = 0.0;
            }
            if(empty($_POST['GTA'])){
                $GTA = "";
            }
            if(empty($_POST['resume'])){
                $resume = "";
            }
            $studentID = $session->getID();

            $profile_complete = 1;
            $stmt = "Update student set grade_level = '$currentLevel', graduation_year= '$graduatingSemester', 
                   major = '$currentMajor', transcript = '$transcript', gpa = '$gpa', hours_completed = '$currentHours', profile_complete = '$profile_complete', 
                   resume = '$resume', gta_certification = '$GTA', ";


            if(empty($GTA)){
                $stmt .= "GTA_certified= 0 where student_id = '$studentID'";
            }
            else{
                $stmt .= "GTA_certified= 1 where student_id = '$studentID'";
            }

            $session->consoleLog($stmt);
            mysqli_query($session->getLink(), $stmt);

            echo '<script> alert("Profile has been updated") </script>';
            if(isset( $_SESSION["input"])){
                unset($_SESSION["input"]);
            }
        }
        else{
            echo '<script> alert(" '.$submission_err.'") </script>';
            $_SESSION["input"] = $input;
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Submit</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="style.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <style>
        .login{
            margin: auto;
            color: white;
            padding: 10px;
            border: none;
            position: relative;
            top: 120px;
            width: 43vw;
            display:table;
            height: 40vh;
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


<?php
$studentID = $session->getID();
$sql_student = "select * from student where student_id ='$studentID'";
$result = mysqli_query($session->getLink(), $sql_student);
$row = mysqli_fetch_array($result);
$full_name = $row["first_name"] ." ". $row['last_name'];
$insert = "";
$grade_level_name="";
$grade_level ="";
$major = 0;
$major_name="";
$profile_complete= $row["profile_complete"];
if($profile_complete == 1){

    $insert = true;
}

if($insert or isset( $_SESSION["input"])){
    if($insert){

        $grade_level = $row['grade_level'];
        $hoursCompleted = $row["hours_completed"];
        $gpa = $row["gpa"];
        $grade_sem = $row['graduation_year'];
        $transcript = $row["transcript"];
        $gtaCertification= $row["gta_certification"];
        $resume= $row["resume"];
        $major = $row['major'];

    }
    if(isset($_SESSION['input'])){

        $input = $_SESSION['input'];
        $gpa= $input['gpa'];
        $grade_level= $input['currentLevel'];
        $resume = $input['resume'];
        $hoursCompleted = $input['currentHours'];
        $transcript= $input['transcript'];
        $gtaCertification= $input['GTA'];
        $major = $input['currentMajor'];
        $grade_semester = $input['grad_semester'];
    }

    if($grade_level == 0){
        $grade_level_name= "Undergraduate";
    }
    if($grade_level == 1){
        $grade_level_name= "MS";
    }
    if($grade_level == 2){
        $grade_level_name= "PhD";
    }

    $major_name=0;
    if($major == 0){
        $major_name= "CS";
    }
    if($major == 1){
        $major_name= "IT";
    }
    if($major== 2){
        $major_name= "ECE";
    }
    if($major== 3){
        $major_name= "EE";
    }

}

?>


<div class="login">
    <h1>Update Profile</h1>
    <p style="color:white;">*note in order to apply for lab instructor positions you must have a GTA Certification<br>
        <a style= "color: white; text-underline: white; text-decoration: underline;" href="https://catalog.umkc.edu/general-graduate-academic-regulations-information/international-graduate-student-academic-regulations/">Click here for more info</a>
    </p>
    <hr class ="solid">
    <form action = "" method = "post" style= "padding-left: 40px; padding-right: 40px; padding-top: 10px; ;"><div style="display: table-cell;" ><label>Current Level: </label><select name="currentLevel" required>
                <?php if(!$insert or !isset($_SESSION["input"])) {echo '<option disabled selected value> -- select an option -- </option>  <option value="0">Undergraduate</option>
                <option value="1">MS</option>
                <option value="2">PhD</option> ';}
                else{
                    echo'<option value="'.$grade_level.'">'.$grade_level_name.'</option>';
                    if($grade_level == 1){
                        echo ' <option value="0">Undergraduate</option>
                <option value="2">PhD</option>';
                    }
                    if($grade_level == 0){
                        echo '
                <option value="1">MS</option>
                <option value="2">PhD</option>';
                    }
                    if($grade_level == 2){
                        echo ' <option value="0">Undergraduate</option>
                <option value="1">MS</option>
             ';
                    }
                }

                ?>
            </select></div>
        <div style="display: table-cell; padding-left: 20px;">  <label>Graduating Semester:</label> <?php if($insert){echo'<input type="number" name="graduatingSemester" min="2022" max="2030"  value ="'.$grade_sem.'"required>';}  else{echo '<input type="number" name="graduatingSemester"  min="2022" max="2030" required>';} ?> </div>
        <br>
        <div style="display: table-cell;" ><label>UMKC Cumulative GPA (leave blank if first semester is in progress) :</label><?php if($insert or !(isset($_SESSION["input"]))) {echo'<input type="number" step="0.01" name="cumulativeGPA" min="0" max="1000" value ="'.$gpa.'">';}  else{echo '<input type="number" step="0.01" name="cumulativeGPA" min="0" max="1000">';} ?></div>

        <div style="display: table-cell; padding-left: 20px;"> <label>Hours Completed at UMKC (leave blank if first semester is in progress): </label><?php if($insert or !(isset($_SESSION["input"]))){echo'<input type="number" name="hoursCompleted" value="'.$hoursCompleted.'">';}  else{echo '<input type="number" name="hoursCompleted">';} ?></div>

        <br>
        <div style="display: table-cell;" >
            <label>Current Major(CS/IT/ECE/EE):</label><select name="currentMajor" required>
                <?php if(!$insert or !isset($_SESSION["input"])) {echo '< <option disabled selected value> -- select an option -- </option>
            <option value="0">CS</option>
            <option value="1">IT</option>
            <option value="2">ECE</option>
            <option value="3">EE</option> ';}
                else{
                    echo'<option value="'.$major.'">'.$major_name.'</option>';
                    if($major == 1){
                        echo '<option value="0">CS</option>
            
            <option value="2">ECE</option>
            <option value="3">EE</option>';
                    }
                    if($major == 0){
                        echo '
          
            <option value="1">IT</option>
            <option value="2">ECE</option>
            <option value="3">EE</option>';
                    }
                    if($major == 2){
                        echo '  <option value="0">CS</option>
            <option value="1">IT</option>
            <option value="3">EE</option>
             ';
                    }
                    if($major == 3){
                        echo ' <option value="0">CS</option>
            <option value="1">IT</option>
            <option value="2">ECE</option>
             ';
                    }
                }

                ?>
            </select></div>
        <div style="display: table-cell; padding-left: 20px;">
            <label>Are you an international student, WITHOUT a degree from a U.S. University:</label><select  name="International" required>
                <option disabled selected value> -- select an option -- </option>
                <option value="1">Yes</option>
                <option value="0">No</option></select></div>


        <br>
        <label>Link Your Unofficial Transcript:</label><?php if($insert or !(isset($_SESSION["input"]))){echo'<input type="text"  name="transcript" required value="'.$transcript.'">';}  else{echo '<input type="text"  name="transcript" required>';} ?>
        <br>
        <br>
        <label>Link Your Resume *optional</label><?php if($insert or !(isset($_SESSION["input"]))){echo'<input type="text"  name="resume" value="'.$resume.'">';}  else{echo '<input type="text"  name="resume">';} ?>
        <br>
        <br>
        <label>Upload GTA Certification (Required for international students without degree from U.S. university and for all lab instructor positions) </label>
        <?php if($insert or !(isset($_SESSION["input"]))){echo'<input type="text"  name="GTA"  value="'.$gtaCertification.'">';}  else{echo '<input type="text"  name="GTA">';} ?>


        <input type = "submit" name ="continue" value = "Continue";>
    </form>
</div>

</body>




