<?php
//
//if check if on application pageAll if yes then output extra if
const DB_SERVER = "cs456webapp.mysql.database.azure.com";
const DB_USERNAME = "tarngp";
const DB_PASSWORD = "xUmzF5Av3@2U";
const DB_NAME = 'cswebapp';

class UserSession
{
    private $role;
    private $user_id;
    private $email_address;
    public $majorChoice;
    public $posChoice;
    public $degreeLevelChoice;
    public $maximumGpaChoice;
    public $minimumGpaChoice;
    private $first_name;
    private $last_name;
    private $id;
    public $viewAllApps;
    public $OnApplicationPage= false;
    public $OnApplicationPageAccepted= false;

    private $year;
    private $semester;
    private $isFullTable = true;//Helps later to decide if we want to output the full table or not
    public $listingUpdate="";//Listing ID of Update
    public $editCourseID="";//courseID for edit
    public $profIDAdmin="";//prof ID for editing
    public $inputs = []; //used for collecting inputs in forms

    public function __construct()
    {
        $this->viewAllApps = true;
    }
    public function setApplicationPage(){
        $this->OnApplicationPage= true;
    }
    public function unsetApplicationPage(){
        $this->viewAllApps = true;
        $this->OnApplicationPage = false;
    }


    //returns true if a user logged in successfully
    public function isLoggedIn(): bool
    {
        if (isset($this->role)) {
            return true;
        } else {
            return false;
        }
    }


    //returns true if the user is logged in with the student role
    public function isStudent(): bool
    {
        if ($this->isLoggedIn() && $this->role == 2) {
            return true;
        } else {
            return false;
        }
    }

    //returns true if the user is logged in with the admin role
    public function isAdmin(): bool
    {
        if ($this->isLoggedIn() && $this->role == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isManagingAdmin(): bool {
        if($this->isLoggedIn() && $this->role == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function printingFullTable($bool){//whether or not to print full table on professor side
        if($bool == 1){
            $this-> isFullTable = true;
        }
        else{
            $this->isFullTable =  false;
        }
    }

    public function getTableStatus(){
        if($this->isFullTable){
            echo'<br><h1 style="text-align: center; font-family: sans-serif;">All Current Active Listings</h1>';
        }
        else{
            echo'<br><h1 style="text-align: center; font-family: sans-serif;">All Current Listings for '.$this->semester.' '.$this->year.'</h1>';
        }
    }

    public function listingSemester($timeOfYear){//$status either all or semester year// if not 'All' we will set semester and year so we can filter the table by it
        if($timeOfYear == "All"){
            $this -> printingFullTable(1);//print full table. listingsemester is set to all currently so we know what to query later
        }
        else{
            $lastSpace = strpos($timeOfYear, " "); //string slicing to get time of year
            $year = substr ($timeOfYear, $lastSpace + 1);
            $semester = substr ($timeOfYear, 0, $lastSpace);
            $this -> printingFullTable(0);//print table based on correct info
            $this ->year= $year;
            $this->semester = $semester;
        }
    }

    public function getSeasonRepeats(){//gives lists of all seasons involved with classes
        $sql="";
        if($this-> isManagingAdmin()){
            $admin_id= $this -> id;
            $sql = "select * from professor_teaches where managing_admin = '$admin_id' ";
        }
        if($this-> isAdmin())
        {
            $professor_id = $this ->id;
            $sql =   $sql = "select * from professor_teaches where professor_ID = '$professor_id'";
        }
        $result = $this->dbQuery($sql);
        $output = '<form style="position: relative; left: 155px; top: 160px" method="post" action="">';
        $seasons = [];
        while($row = mysqli_fetch_array($result)){
            $semester = trim($row['semester']);
            $year = $row['year'];
            $season = $semester ." ". $year;
            if(!in_array($season, $seasons)) {

                $seasons[] = $season;
            }

        }
        $output .= '<label>Semester: </label><select  name="semesterChoice" style="width: 200px"> <option value ="All">All Listings</option>';
        for($i=0; $i < sizeof($seasons); $i++){
            $output .= '<option value="'.$seasons[$i].'"> '.$seasons[$i].'</option>';

        }
        $output .= '</select><input type="submit" name="listingSeason" value="Submit"></form> <br><br>';

        echo $output;

    }

    public function getGradeLevel($num){//used to return the string of the grade level
        if ($num  == 0) {
            return "Undergraduate";
        }
        if ($num == 1) {
            return  "Masters";
        }
        if ($num== 2) {
            return "PhD";
        }

    }

    private Function getMajor($num){//used to return the string of the grade level
        if ($num  == 0) {
            return "Undergraduate";
        }
        if ($num == 1) {
            return  "Masters";
        }
        if ($num== 2) {
            return "PhD";
        }
    }

    public Function printTablesAdmin(){// print the table data;
        $sql = "
        select listing_id, gpa_requirement, listing.professor_ID, grade_level_requirement, completed_hours_requirement, 
        listing.semester, listing.year, listing.course_ID, listing.position_type from listing 
        inner join professor_teaches on professor_teaches.professor_ID = listing.professor_ID and
        professor_teaches.course_id  = listing.course_ID and professor_teaches.semester = listing.semester and 
        professor_teaches.year = listing.year ";//first get  the majority of sql
        if($this->isManagingAdmin()){//admin we check by managing Admin
            $sql.="where professor_teaches.managing_admin ='$this->id' "; //concat based on if admin or not
        }
        else{//if professor == true
            $sql .= "where professor_teaches.professor_ID ='$this->id' ";
        }
        if(!$this->isFullTable){//not the entire table: it's filtered concat last extra line same code for both
            $sql .= "and listing.semester = '$this->semester' and listing.year = '$this->year'";
        }
        $result = $this->dbQuery($sql);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $listing_id = $row["listing_id"];
                $sql = "select count(*) as applicants from application where rejected_flag = 0 and accepted_flag=0 and listing_id = '$listing_id'";//get count to get # of Applicants
                $num = $this->dbQuery($sql);//retrieve that count
                $row3 = mysqli_fetch_array($num);
                $num_applicants = $row3["applicants"];//get the num from here
                if($this->isManagingAdmin()){
                    $instructor = $row["professor_ID"];//
                    $professor_query = "select professor_first_name, professor_last_name from professor where professor_ID = '$instructor'";
                    $professor_query_result = $this->dbQuery($professor_query);
                    $professor_name = mysqli_fetch_array($professor_query_result);
                    $professor_full_name = $professor_name["professor_first_name"] ." ". $professor_name["professor_last_name"];
                }
                $course_id = $row["course_ID"];
                $sql2 = "select course_name, course_number from course where course_id = '$course_id'";//get course info like name
                $result2 = $this->dbQuery($sql2);
                $row2 = mysqli_fetch_array($result2);
                $course_name = $row2["course_name"];
                $course_number = $row2["course_number"];
                $semesterYear = $row['semester'] . " " . $row ["year"];
                $gpa = $row["gpa_requirement"];
                $grade_requirement = $this->getGradeLevel(   $row["grade_level_requirement"] );//obtain string for grade Level requirement
                if($this->isAdmin()){
                    $output = '
                    <tr> <td> <form action = "" method = "post" > <input type= "hidden" name = "listing_id" value = "' . $row["listing_id"] . '">
                    <input type = "submit" name = "course" value = " ' . $course_number . ' - ' . $course_name . '"> </form>
                    </td> <td> ' . $gpa . ' </td> <td>'.$row["completed_hours_requirement"].'</td><td> ' . $grade_requirement . ' </td>  <td>' . $row["position_type"] . '</td> <td>'.$semesterYear.'</td> <td>' . $num_applicants . '</td>
                    <td><form action = "" method="post"><input type = "hidden" name = "update" value = "' . $row["listing_id"] . '"> 
                    <input type="image" src = "edit.svg" width="30px" height = "30px"></form> </td> <td> <form action = "" method = "post" > <input type = "hidden" name = "delete" value = "' . $row["listing_id"] . '"> 
                    <input type = "image" img src = "trash.svg" width="30px" height = "30px" > </form> </td> </tr>';
                    echo $output;
                } else{
                    $output = '
                    <tr> <td> <form action = "" method = "post" > <input type= "hidden" name = "listing_id" value = "' . $row["listing_id"] . '">
                    <input type = "submit" name = "course" value = " ' . $course_number . ' - ' . $course_name . '"> </form> <td>'.$professor_full_name.' </td>
                    </td> <td> ' . $gpa . ' </td> <td>'.$row["completed_hours_requirement"].'</td><td> ' . $grade_requirement . ' </td>  <td>' . $row["position_type"] . '</td> <td>'.$semesterYear.'</td> <td>' . $num_applicants . '</td>
                    <td><form action = "" method="post"><input type = "hidden" name = "update" value = "' . $row["listing_id"] . '"> 
                    <input type="image" src = "edit.svg" width="30px" height = "30px"></form> </td> <td> <form action = "" method = "post" > <input type = "hidden" name = "delete" value = "' . $row["listing_id"] . '"> 
                    <input type = "image" img src = "trash.svg" width="30px" height = "30px" > </form> </td> </tr>';
                    echo $output;
                }
            }
        }
    }

    //returns the link for the database, call this function instead of $link to prevent calling on a closed session
    public function getLink()
    {
        $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($link) {
            return $link;
        }
    }

    public function getID()
    {
        return $this->id;
    }

    private function applicationUpdateQuery($application_id, $acceptFlag, $rejectFlag) {
        if($acceptFlag == 1 && $rejectFlag == 0) {
            $sql = "
            update application 
            set accepted_flag=1, rejected_flag=0 
            where application_id = $application_id";
            $this->dbQuery($sql);
        } elseif ($acceptFlag == 0 && $rejectFlag == 1) {
            $sql = "
            update application 
            set accepted_flag=0, rejected_flag=1 
            where application_id = $application_id";
            $this->dbQuery($sql);
        }
    }

    public function acceptApplication($application_id) {
        $this->applicationUpdateQuery($application_id, 1, 0);
    }

    public function rejectApplication($application_id) {
        $this->applicationUpdateQuery($application_id, 0, 1);
    }

    public function fetchIndexPage(): bool
    {
        $this->displayHeader("UMKC Opportunities");
        $this->displayRequestFilterBar();
        if ($this->getRole() == NULL || $this->getRole() == 2) {
            $this->fetchRequests($this->filterRequest());
            return true;
        } elseif ($this->getRole() == 1 or $this->isManagingAdmin()) {
            $this->fetchRequests($this->filterAdminApplication());

            return true;
        } else {
            return false;
        }
    }

    public function clearRequestFilters()
    {
        $this->majorChoice = [];
        $this->degreeLevelChoice = [];
        $this->posChoice = [];
        $this->minimumGpaChoice = "";
        $this->maximumGpaChoice = "";
    }

    public function filterRequest()
    {
        $filterQuery = "
        SELECT * FROM listing l
        JOIN course c on c.course_id = l.course_ID
        JOIN professor p on p.professor_ID = l.professor_ID
        JOIN grade_level g on l.grade_level_requirement = g.id";
        $filterQuery .= " WHERE ";
        return $this->filter($filterQuery);
    }

    public function filterApplication()
    {
        $filterQuery = "
        SELECT * FROM application a
        JOIN listing l on a.listing_id = l.listing_id
        JOIN course c on c.course_id = l.course_ID
        JOIN professor p on p.professor_ID = l.professor_ID
        JOIN grade_level g on l.grade_level_requirement = g.id";

        if ($this->isStudent()) {
            $filterQuery .= "
            WHERE a.student_id = '$this->id'
            AND ";
        } else {
            $listing_id = $_SESSION['listing_id'];
            $filterQuery .= "
            Join student s on s.student_id = a.student_id 
            WHERE l.listing_id = '$listing_id' and ";
        }
        $this->consoleLog($filterQuery);
        return $this->filter($filterQuery);
    }

    public function filterAdminApplication()
    {
        $filterQuery = "";
        if($this->isAdmin()){
            $filterQuery = "
        SELECT * FROM listing l
        JOIN course c on c.course_id = l.course_ID
        JOIN professor p on p.professor_ID = l.professor_ID
        JOIN grade_level g on l.grade_level_requirement = g.id
        WHERE l.professor_ID = $this->id";

        }
        if($this->isManagingAdmin()){
            $filterQuery ="select * from listing l 
               JOIN professor_teaches pt on pt.year=l.year
         and pt.semester = l.semester and pt.course_id = l.course_id
         and pt.professor_ID = l.professor_ID
         JOIN course c on c.course_id = l.course_id
        JOIN professor p on pt.professor_ID = p.professor_ID
        JOIN grade_level g on l.grade_level_requirement = g.id 
        Where managing_admin ='$this->id'
";
        }

        $this->consoleLog($filterQuery);
        return $this->dbQuery($filterQuery);
    }

    //wrapper function that creates the filter query
    private function filter($filterQuery)
    {

        $majors = ["0", "1", "2", "3"];
        $degrees = ["0", "1", "2"];
        $positions = ['Grader', 'Lab Instructor', 'GTA'];
        $user = "";
        if (empty($this->majorChoice)) {

            $this->majorChoice = $majors;
        } elseif (!is_array($this->majorChoice)) {
            $this->majorChoice = array($this->majorChoice);
        }
        if (empty($this->posChoice)) {

            $this->posChoice = $positions;
        } elseif (!is_array($this->posChoice)) {

            $this->posChoice = array($this->posChoice);
        }
        if (empty($this->degreeLevelChoice)) {
            $this->degreeLevelChoice = $degrees;
        } elseif (!is_array($this->degreeLevelChoice)) {

            $this->degreeLevelChoice = array($this->degreeLevelChoice);
        }
        if (empty($this->minimumGpaChoice)) {
            $this->minimumGpaChoice = 0;
        }
        if (empty($this->maximumGpaChoice)) {
            $this->maximumGpaChoice = 4.0;
        }
        $this->posChoice = "'" . implode("', '", $this->posChoice) . "'";
        $this->degreeLevelChoice = implode(", ", $this->degreeLevelChoice);
        $this->majorChoice = implode(", ", $this->majorChoice);
        if($this->isStudent() or $this->role == NULL){
            $filterQuery .= "
        l.position_type in ($this->posChoice) 
        AND c.major in ($this->majorChoice)
        AND grade_level_requirement in ($this->degreeLevelChoice)
        AND gpa_requirement >= $this->minimumGpaChoice
        AND gpa_requirement <= $this->maximumGpaChoice";

        }
        else{
            $filterQuery .= "
        l.position_type in ($this->posChoice) 
        AND c.major in ($this->majorChoice)
        AND grade_level in ($this->degreeLevelChoice)
        AND gpa >= $this->minimumGpaChoice
        AND gpa  <= $this->maximumGpaChoice";


        }

        if($this->OnApplicationPage and $this->viewAllApps){//if all then do all
            $filterQuery .= " And a.accepted_flag = 0 and a.rejected_flag = 0";
        }
        if($this->OnApplicationPage and !$this->viewAllApps){
            $filterQuery .= " And a.accepted_flag = 1";
        }



        return $this->dbQuery($filterQuery);
    }

    //sets the student variables when a student logs in so that you can filter by them
    private function setStudent()
    {
        $studentQuery = "SELECT major, grade_level, gpa, first_name, last_name, student_id FROM student WHERE user_id = $this->user_id";
        $result = $this->dbQuery($studentQuery);
        $student = mysqli_fetch_array($result);
        $this->majorChoice = array($student['major']);
        if ($student['grade_level'] == 0) {
            $this->degreeLevelChoice = array(0);
        } elseif ($student['grade_level'] == 1) {
            $this->degreeLevelChoice = array(0, 1);
        }
        $this->id = $student['student_id'];
        $this->maximumGpaChoice = $student['gpa'];
        $this->first_name = $student['first_name'];
        $this->last_name = $student['last_name'];
    }

    //sets the admin variables when an admin logs in
    private function setAdmin()
    {
        $adminQuery = "SELECT professor_first_name, professor_last_name, professor_id FROM professor WHERE user_id = $this->user_id";
        $result = $this->dbQuery($adminQuery);
        $admin = mysqli_fetch_array($result);
        $this->first_name = $admin['professor_first_name'];
        $this->last_name = $admin['professor_last_name'];
        $this->id = $admin['professor_id'];
    }

    private function setManagingAdmin(){//for when administrators who manage listings log in set's their variables as well
        $adminQuery = "SELECT first_name, last_name, admin_id FROM admin WHERE user_id = $this->user_id";
        $result = $this->dbQuery($adminQuery);
        $admin = mysqli_fetch_array($result);
        $this->first_name = $admin['first_name'];
        $this->last_name = $admin['last_name'];
        $this->id = $admin['admin_id'];
    }

    //checks for empty inputs then sanitizes them and calls the login function
    //returns an array with password and email errors if the login is unsuccessful
    public function checkLoginInputs($email_address, $password): bool
    {
        $email_error = false;
        $password_error = false;
        // Check if username is empty
        if (empty(trim($email_address))) {
            $email_error = true;
        } else {
            $email_address = trim($_POST["email"]);
        }
        // Check if password is empty
        if (empty(trim($password))) {
            $password_error = true;
        } else {
            $password = trim($_POST["password"]);
        }
        // Validate credentials
        if (($email_error) && ($password_error)) {
            echo '<script> alert ("Please enter your email address and password") </script>';
            return false;
        } elseif ($email_error) {
            echo '<script> alert ("Please enter your email address") </script>';
            return false;
        } elseif ($password_error) {
            echo '<script> alert ("Please enter your password") </script>';
            return false;
        } else {
            return $this->login($email_address, $password);
        }
    }

    //displays the html header to change the page title and ensure all pages have the same css
    public function displayHeader($title)
    {
        echo "
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
            <link rel='stylesheet' href='style.css'>
            <title>$title</title>
        </head>
        <body style='background-color: #f0f0f0;'>
        ";
        $this->displayNavigationBar();
    }

    //returns 0, 1, 2 if a user is logged in
    public function getRole()
    {
        return $this->role;
    }

    //mostly for testing, outputs logs to the web browser console
    public function consoleLog($output)
    {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        $js_code = '<script>' . $js_code . '</script>';
        echo $js_code;
    }

    //dynamically display links at the top of the page based on user status
    private function displayNavigationBar()
    {
        echo "
        <div class='topnav'>
        <div class='search-container'>
            <form>
                <input type='text' placeholder='Search...' name='search'>
                <button type='submit'><i class='fa fa-search'></i></button>
            </form>
        </div>
        ";

        if ($this->isLoggedIn()) {
            echo "
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='logout.php'><u>Logout</u></a>
            ";
        }

        if ($this->role == 1) {
            echo "
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='ClassList.php'><u>Add Listing</u></a>
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='Professor_Homepage.php'><u>View Listings</u></a>
            ";
        }
        if($this->isManagingAdmin()){
            echo "
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='ClassList.php'><u>Add Listing</u></a>
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='Professor_Homepage.php'><u>View Listings</u></a>
            ";

        }

        if ($this->role == 2) {
            echo "
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='applications.php'><u>Applications</u></a>
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='profile.php'><u>Profile</u></a>";
        }

        if (!$this->isLoggedIn()) {
            echo "
            <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='login.php'><u>Login</u></a>";
        }
        echo "
        <a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='index.php'><u>Home</u></a>
        <p style='padding-left: 20px'> <img src='refreshed-umkc-logo.png' width='120' height = '70' >
        </div>
        ";
    }

    private function setRole($role) {
        $this->role = $role;
    }

    private function loginQuery($email_address) {
        $userQuery = "SELECT * FROM cswebapp.user WHERE email_address = '$email_address'";
        $result = $this->dbQuery($userQuery);
        if(!empty($result)){
            return $result;
        } else {
            echo '<script> alert ("Invalid Username") </script>';
        }
    }

    private function setUserID($id) {
        $this->user_id = $id;
    }

    private function setEmail($email) {
        $this->email_address = $email;
    }

    private function setUserDetails($role) {
        if ($role == 2) {
            $this->setStudent();
        } elseif ($role == 1) {
            $this->setAdmin();
        }
        elseif($role ==0){
            $this->setManagingAdmin();
        }
    }

    private function testPassword($result, $password): bool {
        if(!empty($result)) {
            $row = mysqli_fetch_array($result);
            if($row['password'] == md5($password)) {
                $this->setUserID($row['user_id']);
                $this->setRole($row['role']);
                $this->setEmail($row['email_address']);
                if($row['role'] == 2) {
                    $this->setStudent();
                } elseif ($row['role'] == 1) {
                    $this->setAdmin();
                }
                elseif($row['role' == 0]){
                    $this->setManagingAdmin();
                }
                return true;
            } else {
                echo '<script> alert ("Wrong Password") </script>';
                return false;
            }
        } else {
            return false;
        }
    }

    //submits sanitized inputs and returns error message if unsuccessful
    private function login($email_address, $password): bool
    {
        $result = $this->loginQuery($email_address);
        if($this->testPassword($result, $password)) {

            return true;
        } else {
            return false;
        }
    }

    //calls on the listings in the database
    public function fetchRequests($result)
    {
        echo '<br><br><br><br><br><br>';
        if (mysqli_num_rows($result) == 0) {
            $this->displayEmptyResult();
        } else {
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $row = mysqli_fetch_array($result);
                $this->displayRequest($row["position_type"], $row['course_number'], $row['course_name'], $row["semester"],
                    $row['year'], $row['course_description'], $row['professor_last_name'], $row['professor_first_name'],
                    $row['gpa_requirement'], $row['name'], $row["completed_hours_requirement"], $row['listing_id']);
            }
        }
    }

    private function displayEmptyResult()
    {
        echo "<p style = 'text-align: center'> Nothing to show Here</p>";
    }

    //html code for displaying requests
    private function displayRequest($position_type, $class_number, $class_name, $semester, $year, $description,
                                    $professor_first_name, $professor_last_name, $gpa, $grade, $hours_competed,
                                    $listing_id)
    {
        $outputting = true;
        if($this->isStudent()){
            $sql = "select * from application where listing_id = '$listing_id' and student_id = ' $this->id'";
            $result = $this->dbQuery($sql);
            if(mysqli_num_rows($result) != 0){//meaning there's a listing app for this student
                $outputting = false;
            }

        }
        if ($outputting){
            $output ='
                <div style="padding-left: 20px">
                <form method = "post" action = "" ">
                <div class ="application" style = "align: center;"> <p style=" text-align: left; padding-left: 20px; padding-top: 10px; color: #0173bc; font-family: sans-serif; font-size: 15px;"> <strong> ' . $position_type . ' Wanted for ' . $class_number . ' - ' . $class_name . ' ' . $semester . ' ' . $year . ' </strong></p>
                <hr class="solid">
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; line-height: 1.5;"> <strong>Class Description: </strong>  ' . $description . '</p>
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;"> <strong> Professor: </strong> ' . $professor_last_name . ' ' . $professor_first_name . ' </p>
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 14px; color: black"> <strong>Requirements:</strong> </p>
                <p style="text-align: left; padding-left: 20px; font-family: sans-serif; font-size: 15px; color: black;"> GPA: ' . $gpa . ' <br> 
                <br>Grade Level: ' . $grade . ' <br> <br> Hours Completed: ' . $hours_competed . '
                <input type="hidden" id="app" name="Applicant" value= "' . $listing_id . '">
                
                ';
            if($this->getRole() == 2 or $this->getRole() == NULL){
                $output .= '<input type = "submit" name = "apply" style = "float: right; margin: 10px;" value = "Apply">';
            }

            $output .= '</div></form></div>';
            echo "$output";

        }



    }

    //displays the filter bar
    public function displayRequestFilterBar()
    {
        echo '
        <div class = "sidebarb"><form action = "index.php" method = "post" style = "padding-right-: 40px; right-padding: 30px; color: white;">
   
        <h3>Major</h3>
          
           <hr class="solid"> <input type="checkbox" id="CS" name="major[]" value= "0">
            <label for="CS"> Computer Science</label>
            <br>
            <input type="checkbox" id="IT" name="major[]" value="1">
            <label for="IT"> Information Technology</label><br>
            <input type="checkbox" id="ECE" name="major[]" value="2">
            <label for="ECE"> Electrical and Computer Engineering </label>
            <br>
            <input type="checkbox" id="EE" name="major[]" value="3">
            <label for="EE"> Electrical Engineering </label><br>
            <h3>Position Type</h3>
            <hr class="solid> ">
            <input type="checkbox" id="Grader" name="pos[]" value="Grader">
            <label for="Grader"> Grader</label>
            <br>
            <input type="checkbox" id="Lab Instructor" name="pos[]" value="Lab instructor">
            <label for="Lab Instructor"> Lab Instructor </label><br>
            <input type="checkbox" id="GTA" name="pos[]" value="GTA">
            <label for="GTA"> GTA </label><br>
            <h3>Degree Level</h3>
            <hr class="solid> ">
            <input type="checkbox" id="Undergraduate" name="DegreeLvl[]" value="0">
            <label for="Freshmen"> Undergraduate </label><br>
            <input type="checkbox" id="Masters" name="DegreeLvl[]" value="1">
            <label for="Sophomore"> Masters </label><br>
            <input type="checkbox" id="PhD" name="DegreeLvl[]" value="2">
            <label for="PhD"> PhD </label><br>
            <h3>GPA</h3>
            <hr class="solid> ">
            <label for="gpaHigher">GPA is at most :</label>
            <input type="number"  step = "0.01" id="gpaHigher" name="gpaHigher" min="0" max=4.0">
            <br>
            <label for="gpaLower">GPA is at least :</label>
            <input type="number"  step = "0.01" id="gpaLower" name="gpaLower" min="0" max=4.0">
            <br>
            <input type = "submit" name = "filter submit" style = "alignment: center" value = "Submit">
        </form> </div>';
    }

    //wrapper function for database queries
    private function dbQuery($query)
    {
        return mysqli_query($this->getLink(), $query);
    }

    public function displayApplicationFilterBar()
    {
        echo '<div class = "sidebarb"> <h2 style = "text-align: center; color: white;">Filter</h2><form action = "" method = "post" style = "padding-right-: 40px; right-padding: 30px; color: white;">
        <h3>Degree Level</h3>
        <hr class="solid> ">
        <input type="checkbox" id="Undergraduate" name="DegreeLvl[]" value="0">
        <label for="Undergraduate"> Undergraduate </label><br>
        <input type="checkbox" id="Masters" name="DegreeLvl[]" value="1">
        <label for="Masters"> Masters </label><br>
        <input type="checkbox" id="PhD" name="DegreeLvl[]" value="2">
        <label for="PhD"> PhD </label><br>
         <h3>GPA</h3>
        <hr class="solid> ">
        <label for="gpaHigher">GPA is at most :</label>
        <input type="number"  step = "0.01" id="gpaHigher" name="gpaHigher" min="0" max=4.0"><br>
        <label for="gpaLower">GPA is at least :</label>
        <input type="number"  step = "0.01" id="gpaLower" name="gpaLower" min="0" max=4.0"><br>
        <input type = "submit" name = "filter submit" style = "float: right;" value = "Submit">
    </form> </div>';
    }

    public function adminClassList(){//print class list based on prof and Class
        $course_name = "";
        $repeatCourse = [];
        $professor = [];
        $repeat = false;
        $sql = "select course_id, professor_ID from professor_teaches where managing_admin= '$this->id'";//select all courses where managing_admin = current admin
        $result =  $this->dbQuery($sql);
        while($row = mysqli_fetch_array($result)){
            $repeat = false;
            if(!in_array($row["course_id"], $repeatCourse) and !in_array($row["professor_ID"], $professor)){
                $professor[] = $row["professor_ID"];
                $repeatCourse[] = $row["course_id"];

            }
            elseif(in_array($row["course_id"], $repeatCourse) or in_array($row["professor_ID"], $professor)){
                for($i = 0; $i<count($repeatCourse); $i++){
                    if($repeatCourse[$i] == $row["course_id"] and $professor[$i] == $row["professor_ID"]){
                        $repeat = true;
                        break;
                    }

                }
                if(!$repeat){
                    $professor[] = $row["professor_ID"];
                    $repeatCourse[] = $row["course_id"];
                }

            }
        }
        $this->printRows($repeatCourse,$professor);

    }

    public function profClassList(){
        $repeatCourse=[];
        $sql = "select course_id from professor_teaches where professor_ID = '$this->id'";
        $result = $this->dbQuery($sql);
        while($row = mysqli_fetch_array($result)){

            if(!in_array($row["course_id"], $repeatCourse)){
                $repeatCourse[] = $row['course_id'];
            }
        }
        $this->printRows($repeatCourse);
    }

    private function printRows($repeatCourse, $professor=[]){
        for($i = 0; $i < sizeof($repeatCourse); $i++) {
            $sql = "select course_name, course_number from course where course_id = '$repeatCourse[$i]'";
            $result = $this->dbQuery($sql);
            $row = mysqli_fetch_array($result);
            $course_number = $row['course_number'];
            $course_name = $row['course_name'];
            if($this->isManagingAdmin()){// if admin we need to get the professor id and the course ID so we have to query twice for them
                $professor_query = "select * from professor where professor_ID = '$professor[$i]'";
                $professor_result = $this->dbQuery($professor_query);
                $row_prof =  mysqli_fetch_array($professor_result);
                $full_name = $row_prof["professor_first_name"] ." ".$row_prof["professor_last_name"];

                $output = '<tr><td>'.$course_number.' - '.$course_name.'</td> <td>'.$full_name.'</td>  <td><form action = "" method="post"><input type = "hidden" name = "update" value = "' . $repeatCourse[$i] . ' ' . $professor[$i] . '"> 
          <input type="image" src = "edit.svg" width="30px" height = "30px"></form></td></tr>';

                echo $output;
            }
            else{//for professors, they don't need an extra column like admins for professor
                $output = '<tr><td>'.$course_number.' - '.$course_name.'</td> <td><form action = "" method="post"><input type = "hidden" name = "update" value = "' . $repeatCourse[$i] . '"> 
          <input type="image" src = "edit.svg" width="30px" height = "30px"></form></td></tr>';
                echo $output;
            }

        }


    }

    public function setListingUpdate($listing_id){//set the listing_id for update page
        $this->listingUpdate =  $listing_id;

    }

    public function setEditCourseID($course_id, $prof_id = 0){//$prof_id only needed if we are an administrator
        $this->editCourseID = $course_id;
        if($this->isManagingAdmin()){
            $this->profIDAdmin = $prof_id;// use this to make listing for correct person
        }
    }

    public function addCourseChoice(){//for getting listing attributes
        $course_ID = $this->editCourseID;
        $sql = "select * from course where course_id = '$course_ID'";
        $result = $this->dbQuery($sql);
        $row = mysqli_fetch_array($result);
        $course_name = $row["course_name"];
        $course_number = $row["course_number"];
        echo '<h1 style="text-align: center;"> Add '.$course_number.' - '.$course_name.' Listing </h1>';
        echo '<p style="text-align: center">*Note creating a listing with the same course name and year/semester as a current listing will result in an update to that listing</p>';
        echo'<div class="login">';

        if($this->isAdmin()){//first we are performing query based on role
            $sql = "select * from professor_teaches where professor_ID = '$this->id' and course_id = '$course_ID'";
        }
        else{ //admin is a little different if they have

            $sql = "select * from professor_teaches where managing_admin = '$this->id' and course_id = '$course_ID' and professor_ID = '$this->profIDAdmin'";
        }
        $output = '<div > Choose a semester and year for your listing: <br><br>';//below code gets the availble semester and years for a paticular course offering
        $result =$this->dbQuery($sql);
        if(mysqli_num_rows($result) ==1){//Only one result then output that to the user
            $row = mysqli_fetch_array($result);
            $semester = $row['semester'];
            $year = $row['year'];
            echo "<div> <h2> Managaing listing for ".$semester."  ".$year."</h2></div> <input type ='hidden' name='semesterYear[]' value= '".$semester." ".$year."'><br>";
        }
        else{
            while($row = mysqli_fetch_array($result)){//if multiple results output all possibilities
                $semester = $row['semester'];
                $year = $row['year'];
                $output .= '<input type="checkbox" id="Year" name="semesterYear[]" value="'.$semester.' '.$year.'">
                         <label for="Year"> '.$semester.' '.$year.' </label> ';
            }
            $output .= "</div> <br>";
            echo $output;
        }
        $output = '<div style="display: table-cell; " > <label >UMKC Cumulative GPA Requirement: </label><input type="number" step="0.01" name="cumulativeGPA"  required></div>
        
  
        <div style="display: table-cell; padding-left: 20px;">  <label>Recommended Hours Completed at UMKC </label><input type="number" name="hoursCompleted"></div>';
        echo $output;//display choices
    }

    public Function updateCourseChoice(){//when an update is occuring
        $listing_id = $this->listingUpdate;
        $sql = "select * from listing where listing_id = '$listing_id'";
        $result = $this->dbQuery($sql);
        $row = mysqli_fetch_array($result);
        $this->profIDAdmin = $row['professor_ID'];
        $course_id = $row["course_ID"];
        $sql = "select * from course where course_id = '$course_id'";
        $result = $this->dbQuery($sql);
        $row = mysqli_fetch_array($result);
        $course_name = $row["course_name"];
        $course_number = $row["course_number"];

        echo '<h1 style="text-align: center;">Update '.$course_number.' - '.$course_name.' Listing </h1>';
        echo '<p style="text-align: center">*Note creating a listing with the same course name and year/semester as a current listing will result in an update to that listing</p>';
        echo'<div class="login">';
        if(!empty($this->inputs)){
            $gpa = $this->inputs["gpa"];
            $hoursCompleted = $this->inputs['hoursCompleted'];
        }
        $sql = "select * from listing where listing_id ='$listing_id'";//get semester year
        $result = $this->dbQuery($sql);
        $row = mysqli_fetch_array($result);
        $semester = $row['semester'];
        $year = $row['year'];
        if(empty($this->inputs)){//make the gpa this
            $gpa = $row["gpa_requirement"];
            $hoursCompleted = $row['completed_hours_requirement'];
        }
        $output = '<div style="display: table-cell; " > <input type="hidden" name="semesterYear[]" value="'.$semester.' '.$year.'">  <label>UMKC Cumulative GPA Requirement: </label>
        <input type="number" step="0.01" name="cumulativeGPA" value="'.$gpa.'"  required></div>
        <div style="display: table-cell; padding-left: 20px;">  <label>Recommended Hours Completed at UMKC </label><input type="number" name="hoursCompleted" required value = "'.$hoursCompleted.'"></div>';     echo $output;
    }

    public function processAddListing($gpa,$professorID,$currentLevel,$posType,$hoursCompleted,$course_ID,$timeOfYearList){
        for($i = 0; $i< count($timeOfYearList); $i++){//string slicing to get semester and year can make into a function.
            $timeOfYear = $timeOfYearList[$i];
            $lastSpace = strpos($timeOfYear, " ");
            $year = substr ($timeOfYear, $lastSpace + 1);
            $semester = substr ($timeOfYear, 0, $lastSpace);
            $sql = "select * from listing where course_id = '$course_ID' and professor_id = '$professorID' and year = '$year' and semester = '$semester'";
            //Above Code is asking to see if the listing is already stored in the database with that chosen criteria.
            $result = $this->dbQuery($sql);
            if(mysqli_num_rows($result) != 0){//If the answer is yes then it will Update
                $sql2 = "Update listing set grade_level_requirement ='$currentLevel', gpa_requirement= '$gpa', position_type= '$posType', completed_hours_requirement = '$hoursCompleted' 
               where course_id = '$course_ID' and professor_id = '$professorID' and year = '$year' and semester = '$semester'";
                $result = $this->dbQuery($sql2);
            }
            else{//If not then we can insert these values into the database as they are new
                $sql = "insert into listing (course_id, professor_ID, grade_level_requirement,gpa_requirement,position_type, completed_hours_requirement, semester, year) 
                    Values ('$course_ID', '$professorID', '$currentLevel','$gpa','$posType','$hoursCompleted','$semester', '$year')";
                $result = $this->dbQuery($sql);
            }
        }
        $this->listingUpdate = "";//once finished set to empty as we are no longer in that state
        //setting values to empty if edit
        $this->editCourseID="";
        if($this->isManagingAdmin()){
            $this->profIDAdmin =""; //result professorID value as well
        }
        $this->inputs=[];//clear this as well so we don't accidentally fill with wrong info
        echo '<script> alert("Listing has been successfully added!") </script>';
        header("location: Professor_Homepage.php");



    }

    public function checkErrors($course_ID,$posType){
        $sql = "select * from course where course_id = '$course_ID'";
        $error=false;
        $result = $this->dbQuery($sql);
        $row= mysqli_fetch_array($result);
        $submission_err = "";
        if($row["lab_course"] == 1 and ($posType == "Grader" or $posType == "GTA")){
            $submission_err = "This course can only have Lab Instructor Positions";
            $error = true;
        }
        if($row["lab_course"] == 0 and $posType == "Lab instructor") {
            $submission_err = "This course can't have Lab Instructor Positions";
            $error=true;
        }
        return $error;
    }
}
