<?php

require_once 'UserSession.php';

session_start();

if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
    $session->clearRequestFilters();
    $session->unsetApplicationPage();
} else {
    $session = new UserSession();
    $_SESSION['session'] = $session;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!$session->isLoggedIn() and isset($_POST["apply"])){
        header("location: login.php");
    }
    if(isset($_POST["apply"])){
        $applicant = $_POST["Applicant"];
        $student_id = $session->getID();
        $sql = "insert into application (listing_id,student_id,accepted_flag, rejected_flag) values ('$applicant', '$student_id', 0,0)";


        $result = mysqli_query($session->getLink(), $sql);
        echo "<script>alert ('Applied Success!')</script>";
        header('Location: index.php', true, 303);
        exit;
    }
}

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST'){
    if(isset($_POST["gpaLower"])){
        $session->minimumGpaChoice = $_POST["gpaLower"];
    }
    if(isset($_POST["gpaHigher"])) {
        $session->maximumGpaChoice = $_POST["gpaHigher"];
    }
    if (isset($_POST["major"])){
        $session->majorChoice = $_POST["major"];
    }
    if (isset($_POST["pos"])){
        $session->posChoice = $_POST["pos"];
    }
    if (isset($_POST["DegreeLvl"])){
        $session->degreeLevelChoice = $_POST["DegreeLvl"];
    }
}


$session->fetchIndexPage();

