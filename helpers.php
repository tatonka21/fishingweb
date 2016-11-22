<?php
$errors = array();
$dev_host_name_beginnings = Array(
    "1" => "wingsuit");

if (array_search(substr(php_uname("n"),0,8),$dev_host_name_beginnings)) {
    define('DEV', true);
    define('PRODUCTION', false);
} else {
    define('PRODUCTION', true);
    define('DEV', false);
}



function logError($msg) {
    global $errors;
    
    if (DEV) {
            echo "ERROR: ".$msg;
    } else {
        $errors[]=$msg;
    }
}

function error_function ($error_level,$error_message,$error_file,$error_line,$error_context) {
    logError("PHP error, <br> Level: ".$error_level.
                         '<br> Message: '.$error_message.
                         '<br> File: '.$error_file.
                         '<br> Line: '.$error_line.
                         '<br> Context: '.$error_context);    
    return true;
}

set_error_handler("error_function",E_ALL);

function fatal_handler() {
  $errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;

  $error = error_get_last();

  if( $error !== NULL) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];

    error_function($errno, $errstr, $errfile, $errline,'!FATAL!');
    return true;
  }
}
register_shutdown_function( "fatal_handler" );

function deliverErrors() {
    global $errors;
    if (PRODUCTION) {
        ini_set("SMTP","smtp.zone.ee" ); 
        
        $Name = "Ilmainfo koguja"; //senders name 
        $email = 'collector@phi3.eu'; //senders e-mail adress 
        ini_set('sendmail_from', $email); 
        $recipient = "erik.suit@gmail.com"; //recipient 
        $subject = "ERROR: vead ilmainfo kogumiselt"; //subject 
        $mail_body = "Ilmnesid vead: \n"; //mail body 
        foreach ($errors as $error) {
            $mail_body .= $error;
        }
        
        $header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields 
        
        mail($recipient, $subject, $mail_body, $header); //mail command :)        
    }
}
?>
