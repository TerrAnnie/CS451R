<?php

//session_start();
require_once 'UserSession.php';
session_start();
//the second condition is to make sure only admins can view this page
if (isset($_SESSION['session']) && $_SESSION['session']->isLoggedIn()) {


    $session = $_SESSION['session'];
    //added the rest of these variables to reset filter variables
    //otherwise the arrays never get cleared out during the session
    //and you cannot do new filters

    $session->setApplicationPage();


    $session->majorChoice = [];
    $session->degreeLevelChoice = [];
    $session->posChoice = [];
    $session->minimumGpaChoice = "";
    $session->maximumGpaChoice = "";
    //these two lines replace the first part of the html code, will comment that section out.
    $session->displayHeader("Applications");
} else {
    header('location: index.php');
}
//require_once "config.php";
//require_once 'navigationBar.php';


//$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

/*
if (!isset($_SESSION["listing_id"]) and !isset($_SESSION["logged_in"])) {//not logged in and no listing_id chosen
    header("location: login.php");
}

if (!isset($_SESSION["listing_id"]) and isset($_SESSION["logged_in"]) and ($_SESSION['$role'] == 1 or $_SESSION['$role'] == 0)) {//also check for correct role
    header("location: Professor_Homepage.php");

}
if ($_SESSION["role"] != 1 and $_SESSION["role"] != 0) {
    header("location: login.php");
}
*/

$major = "";
$posType = "";
$degreeLevel = "";
$instructor = 0;
$class = "";
$gpa = 0;
$grade = "";
$noFilter = true;
echo '<br><br><br><br><br><br>';
//$listingID = $_SESSION["listing_id"];




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($session)) {
    if (isset($_POST["gpaLower"])) {
        //$gpaChoiceLow = $_POST["gpaLower"];
        $session->minimumGpaChoice = $_POST['gpaLower'];
    }
    if (isset($_POST["gpaHigher"])) {
        //$gpaChoiceHigh = $_POST["gpaHigher"];
        $session->maximumGpaChoice = $_POST['gpaHigher'];
    }
    if (isset($_POST["DegreeLvl"])) {
        //$degreeLevelChoice = $_POST["DegreeLvl"];

        $session->degreeLevelChoice = $_POST['DegreeLvl'];
    }
    if (isset($_POST["application_ID"])) {
        $apply_id = $_POST["application_ID"]; //Probably call a function here or do the query
        if (isset($_POST["Accept"])) {
            $session->acceptApplication($apply_id);
            header('Location: applications.php', true, 303);
            exit;
        }
        if (isset($_POST["Reject"])) {
            $session->rejectApplication($apply_id);
            header('Location: applications.php', true, 303);
            exit;
        }
    }
    if (isset($_POST["viewApps"])) {

        $session->viewAllApps = false;
    }  if (isset($_POST["viewAllApps"])) {

       $session->viewAllApps = true;


    }


}

/* This is duplicated code that was instead called by function in the
 * initial session loop
 *
echo "<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel='stylesheet' href='style.css'>
    <title>UMKC Opportunities</title>
</head>
<body style='background-color: #f0f0f0;'>";
*/

//if have time get course number and name from previous page to avoid query
echo '<h1 style = "text-align: left;"> Active Applications for Course: <br>  </h1>';

if ($session->viewAllApps) {
    echo "<form method='post' action=''> <input type='submit' style=' background: none;
            border: none;
            padding: 0;
            font-size: 20px;
            font-family: arial, sans-serif;
            color: #069;
            text-decoration: underline;
            cursor: pointer;' value='View already Accepted Applications' name='viewApps'></form>";


} else {
    echo "<form method='post' action=''> <input type='submit' style=' background: none;
            border: none;
            padding: 0;
            font-size: 20px;
            font-family: arial, sans-serif;
            color: #069;
            text-decoration: underline;
            cursor: pointer;' value='View Unaccepted Applications' name='viewAllApps'></form>";
}

$session->displayApplicationFilterBar();


$filterGpa = $filterGrade = $filterDegreeLevel = false;
if(isset($session)) {

    if($session->isAdmin()){
        $listingID = $_SESSION["listing_id"];
        $filterQuery = "
    select * from application a
        JOIN listing l on a.listing_id = l.listing_id
        JOIN student s on a.student_id = s.student_id
        JOIN professor p on l.professor_ID = p.professor_ID where l.listing_id = '$listingID' ";

    }
    else{
        $id = $session->getID();
        $filterQuery = "
        select * from application as a join listing on
listing.listing_id = a.listing_id
where student_id ='$id '

     ";
    }

    if (isset($_SESSION["viewingAcceptedApps"])) {
        $filterQuery .= "
        AND a.accepted_flag = 1";

        //inner join student
        //on student.student_id = application.student_id";
        //where (listing_id = '$listingID') and (accepted_flag = 1)";
    } else {
        $filterQuery .= " and rejected_flag = 0 AND a.accepted_flag = 0 
        ";

        //inner join student
        //where (listing_id = '$listingID') and (accepted_flag = 0) and (rejected_flag = 0)";
    }
}




/*
if($session->isStudent()) {
    $id = $session->getID();
    if($noFilter) {
        $filterQuery .= "WHERE s.student_id = $id";
    } else {
        $filterQuery .= "AND s.student_id = $id";
    }
    $session->consoleLog($filterQuery);
} */



$result = [];
if (isset($session)){
    $result = mysqli_query($session->getLink(), $filterQuery);
}
$result = $session->filterApplication();
if (mysqli_num_rows($result) == 0) {
    echo "<p style = 'text-align: center'> Nothing to show Here</p>";
} else {
    for ($i = 0; $i < mysqli_num_rows($result); $i++) {
        if ($session->isAdmin() or $session->isManagingAdmin()) {
        $row = mysqli_fetch_array($result);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $student_ID = $row['student_id'];
        $hours_completed = $row["hours_completed"];
        $listingID = $row ['listing_id'];
        $application_ID = $row["application_id"];
        $gpa = $row['gpa'];
        $resume = $row['resume'];
        $transcript = $row['transcript'];
        $GTA = $row["gta_certification"];
        ///$result2 = "select * from courses where course_nbr = '$class' ";
        ///$query = mysqli_query($link, $result2);
        ///$row2 = mysqli_fetch_array($query);
        ///$class = $row2['name'];
        $grade = $row ['grade_level'];
        if ($grade == 0) {
            $grade = "Undergraduate";
        }
        if ($grade == 1) {
            $grade = "Masters";
        }
        if ($grade == 2) {
            $grade = "PhD";
        }
        /* LOL at what happens when you put div padding in a for loop
        echo '
                <div style="padding-left: 20px">
        */
        echo '
                 <div style="padding-left: 20px">
                <form method = "post" action = "" ">
                <div class ="application" style = "align: center;"> <p style=" text-align: left; padding-left: 20px; 20px; color: #0173bc; font-family: sans-serif; font-size: 15px;"> <strong>Student: ' . $first_name . ' ' . $last_name . ' </strong></p>
                <hr class="solid">
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;""> Student ID: ' . $student_ID . '</p>
               <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 14px; color: black"> <strong> Additional info:</strong> </p>
               <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;""> GPA: ' . $gpa . ' <br> <br>Grade Level: ' . $grade . ' <br> <br>Completed Hours: ' . $hours_completed . ' </p> 
               <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;""> 
               <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;"><strong>Links:</strong></p>
               <p  style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;">
               <a style="text-decoration: underline;" href="'. $transcript .'">Transcript</a><br><br>
                <a style="text-decoration: underline;" href="'. $GTA .'">GTA Certification</a><br><br>
               <a style="text-decoration: underline;" href="' . $resume . '">Resume</a>
               </p>
               <input type="hidden" id="app" name="application_ID" value= "' . $application_ID . '">
              
               ';
        if ($session->viewAllApps) {
            echo ' <input type = "submit"  name = "Reject" style = "background-color: #0173bc ;float: right; margin: 10px; color:white;"  value = "Reject">
               <input type = "submit" name = "Accept" style = "float: right; background-color: #0173bc; color:white; margin: 10px;" data-inline="true" value = "Accept"</div>
             
               ';
        }
            echo '</form></div></div>';


    }
        else {
            $row = mysqli_fetch_array($result);
            $position_type = $row['position_type'];
            $course_id = $row['course_ID'];
            $sql = "Select course_number,course_name, course_description from course where course_id = '$course_id'";
            $result2 = mysqli_query($session->getLink(), $sql);
            $row2 = mysqli_fetch_array($result2);
            $professor_ID = $row['professor_ID'];
            $sql = "Select professor_first_name,professor_last_name from professor where professor_ID = '$professor_ID'";
            $result3 = mysqli_query($session->getLink(), $sql);
            $row3 = mysqli_fetch_array($result3);
            $class_number = $row2["course_number"];
            $class_name = $row2["course_name"];
            $semester = $row["semester"];
            $year = $row["year"];
            $description = $row2["course_description"];
            $professor_first_name = $row3["professor_first_name"];
            $professor_last_name = $row3["professor_last_name"];
            $hours_completed = $row["completed_hours_requirement"];
            $grade = $session->getGradeLevel($row['grade_level_requirement']);

            echo '
                <div style="padding-left: 20px">
                <form method = "post" action = "" ">
                <div class ="application" style = "align: center;"> <p style=" text-align: left; padding-left: 20px; padding-top: 10px; color: #0173bc; font-family: sans-serif; font-size: 15px;"> <strong> ' . $position_type . ' Wanted for ' . $class_number . ' - ' . $class_name . ' ' . $semester . ' ' . $year . ' </strong></p>
                <hr class="solid">
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; line-height: 1.5;"> <strong>Class Description: </strong>  ' . $description . '</p>
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;"> <strong> Professor: </strong> ' . $professor_first_name . ' ' . $professor_last_name . ' </p>
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 14px; color: black"> <strong>Requirements:</strong> </p>
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;"> GPA: ' . $gpa . ' <br> 
                <br>Grade Level: ' . $grade . ' <br> <br> Hours Completed: ' . $hours_completed . '
                </form></div>';
            echo '</div>';

        }

    }
}
?>
</body>
</html>
