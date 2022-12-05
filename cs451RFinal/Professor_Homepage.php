<?php

echo'<br>';

require_once 'UserSession.php';
session_start();

if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
    $session->unsetApplicationPage();
} else {
    $_SESSION = new UserSession();
}

$session->displayHeader("UMKC Opportunities");
$session->listingUpdate = "";
$session->editCourseID= "";

//to-do clicking button sends user to the correct page where they can accept and reject add functionality to see if both are = 0/ then do updates and deletes
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


if (!$link){
    die("Database access failed: " . mysqli_error($link));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST["course"])){
        $_SESSION["listing_id"] = $_POST["listing_id"];

        header("location: applications.php");
    }
    if(isset($_POST["delete"])){
        $listing_id = $_POST["delete"];

        $sql = "delete from listing where listing_id = '$listing_id'";
        $result = mysqli_query($session->getLink(),$sql);



    }
    if(isset($_POST["update"])){
        $session->setListingUpdate($_POST["update"]);
        header("location: Professor_Add_Listing.php");//remember to look at Tony's page to see correct URL

    }

    if(isset($_POST["semesterChoice"])){
        $session-> listingSemester($_POST["semesterChoice"]);

    }




}
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
    <title>Home</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>
<body style="background-color: #f0f0f0;">

<?php
$admin_id = $professor_id="";
$session-> getSeasonRepeats();
$session-> getTableStatus();

?>


<div id="clear">
    <table id="submitTable" style = "margin: auto;">
        <tr>
            <th>Course Name</th>
            <?php if($session->isManagingAdmin()) {echo  "<th>Professor Name</th>";}?>

            <th>GPA Requirement</th>
            <th>Hours Completed Requirement </th>
            <th>Grade Level Requirement </th>
            <th>Position Type</th>
            <th>Semester</th>
            <th>Number of Applicants</th>
            <th>Update Position</th>
            <th>Delete Position</th>

        </tr>

        <?php
        $session->printTablesAdmin();

        ?>


    </table>
</div>

<br> <br>
<div id="searchResult">  </div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#live_search").keyup(function(){
            var input =$(this).val();
            //alert(input);
            if(input != ""){
                $("#clear").css("display","none");
                $.ajax({
                    url:"livesearch.php",
                    method: "POST",
                    data:{input: input},
                    success:function(data){
                        $("#searchResult").html(data);
                        $('#searchResult').show();
                    }

                });
            }
            else{
                $("#searchResult").css("display","none");
                $("#clear").show();
            }
        });
    });

</script>


</body>
</html>

