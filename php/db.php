<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db_name = "quizmaster";

    $conn = mysqli_connect($servername,$username,$password,$db_name);

    if(!$conn){
        die(mysqli_connect_error());
    }
?>