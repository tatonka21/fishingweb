<?php
    
    if (PRODUCTION) {
        $dbUser = 'd60840_collectio';
        $dbPassword = 'sd!v908%w2k2m2f8f#';
        $dbHost='d60840.mysql.zonevs.eu';
        $userDb='d60840_ilmainfo';
    } elseif (DEV) {
        $dbUser = 'root';
        $dbPassword = '';
        $dbHost='127.0.0.1';
        $userDb='d60840_ilmainfo';
    }
    
    
    if (!$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $userDb)){
        logError("Cannot connect to mysql database: \n".$mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    
?>