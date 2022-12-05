<?php
include "config.php";
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
require_once 'UserSession.php';
session_start();
if(isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
} else {
    $_SESSION = new UserSession();
}
if(isset($_POST['input'])){
    $input = $_POST["input"];
    $query = "Select * from course where course_name LIKE '{$input}%' ";

    $result = mysqli_query($link, $query);
    if(mysqli_num_rows($result) >0){?>
        <table id="submitTable" style = "margin: auto;">
            <th>Test</th>
            <th>Test</th></tr>
            <?php
             while($row= mysqli_fetch_array($result)){
                 $course_name = $row["course_name"];
                 $course_id =  $row['course_id'];
                 echo'<tr><td>'.$course_name.'</td><td>'.$course_id.'</td></tr>';
                 }
                 ?>

        </table>




<?php
    } else{
        echo "<h6 class='text-danger text-center mt-3'> NO data found</h6>";
    }
}
?>
