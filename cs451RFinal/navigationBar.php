<?php

echo "<div class='topnav'>

    <div class='search-container'>
   
        <form>
            <input type='text' placeholder='Search..' name='search'>
            <button type='submit'><i class='fa fa-search'></i></button>
        </form>
    </div>";


    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
        echo "<a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='logout.php'><u>Logout</u></a>";
        echo "<a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='applications.php'><u>Applications</u></a>";
        echo "<a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='profile.php'><u>Profile</u></a>";
    } else {
        echo "<a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='login.php'><u>Login</u></a>";
    }
    echo "<a class='active' style='font-size: 23px; color:white; padding: 71px 15px;' href='logout.php'><u>Home</u></a>
<p> <img src='refreshed-umkc-logo.png' width='70' height = '50' > <p style ='text-align: left; color: white; font-family: sans-serif;font-size: 35px; position: fixed; top: 30px;'>Grader Portal</p></p>

</div>
<br>
<br>
<br>
<br>
<br>
<br>
</div>";

