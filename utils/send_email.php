<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';
require_once __DIR__ . '/Exception.php';

function sendEmail($to,$subject,$message){

$mail = new PHPMailer(true);

try{

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;

$mail->Username = 'aruvihostels@gmail.com';
$mail->Password = 'mygx oopp bvfy ceje';

$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('aruvihostels@gmail.com','Hostel Management');

$mail->addAddress($to);

$mail->isHTML(true);

$mail->Subject = $subject;
$mail->Body = $message;

$mail->send();

}catch(Exception $e){

echo "Email failed: ".$mail->ErrorInfo;

}

}