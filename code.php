<?php
session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_verification($name,$email,$verify_token)
{
    $mail = new PHPMailer(true);
    //$mail->SMTPDebug = 2;                      
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = "vanenevanini@gmail.com";                     
    $mail->Password   = "ihcplzilefmqmzop";  
    $mail->SMTPSecure = 'tls';  
    $mail->Port     = 587;  
    
    $mail->setFrom("vanenevanini@gmail.com",$name);
    $mail->addAddress($email);

    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = "This is an email verification from Vanessa's Website";

    $email_template = "
        <h2>You have registered to Vanessa's Website</h2>
        <h5>Verify your given email address by logging in with the below given link</h5>
        <br/><br/>
        <a href ='http://localhost/program/login_signup/verify_email.php?token=$verify_token'> Click This! </a>
    ";

    $mail->Body = $email_template;
    $mail->send();
    //echo 'Message has been sent';

}

if(isset($_POST['register_btn']))
{
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $verify_token = md5(rand());


    //EMAIL VALIDATION (EXIST OR NOT)
    $check_email_query = "SELECT email FROM user WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    if(mysqli_num_rows($check_email_query_run)> 0)
    {
        $_SESSION['status'] = "Email Address Already Exists";
        header("Location: signup.php");
    }
    else
    {
        //Insert User or Registered User Data
        $query = "INSERT INTO user (name,email,phone,password,verify_token) VALUES ('$name','$phone','$email','$password','$verify_token')";
        $query_run = mysqli_query($con, $query);

        if($query_run)
        {
            sendemail_verification("$name","$email","$verify_token");

            $_SESSION['status'] = "Registration Succesful! Please verify your email.";
            header("Location: signup.php");
        }
        else
        {
            $_SESSION['status'] = "Registration Failed! Please Try Again.";
            header("Location: signup.php");
        }
    }
}
?>