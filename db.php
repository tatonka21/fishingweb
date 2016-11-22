<?php
    
require 'auth.php';

    $mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $userDb);
    
    if ($mysqli->connect_errno > 0){
        logError("Cannot connect to mysql database: \n".$mysqli->connect_error);
    }

    
    //var_dump($mysqli);
    
    $mysqli->set_charset("utf8");
    
?>