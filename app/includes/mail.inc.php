<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
require '../vendor/autoload.php';

$mail = new PHPMailer(true);

function setupMailer($mail) {
    try {
        // $mail->SMTPDebug = 2;                                       
        $mail->isSMTP();                                            
        $mail->Host = 'smtp.gmail.com';                    
        $mail->SMTPAuth = true;                             
        $mail->Username = 'caesuraiwu@gmail.com';                 
        $mail->Password = 'hvvs qqwv lxio wrlg';                        
        $mail->SMTPSecure = 'tls';                              
        $mail->Port = 587; 

        $mail->setFrom("caesuraiwu@gmail.com", "Caesura //"); 
    } catch (Exception $e) {
        echo "<p class='header-notif'>Failed to connect to SMTP server. Issue: $mail->ErrorInfo</p>"; 
    }
}



?>