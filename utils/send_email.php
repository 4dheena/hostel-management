<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';
require_once __DIR__ . '/Exception.php';
$env=parse_ini_file(__DIR__.'/../.env');
putenv("MAIL_USERNAME=".$env['MAIL_USERNAME']);
putenv("MAIL_PASSWORD=".$env['MAIL_PASSWORD']);
function sendEmail($to,$subject,$message){

$mail = new PHPMailer(true);

try{

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;

$mail->Username = getenv('MAIL_USERNAME');
$mail->Password = getenv('MAIL_PASSWORD');

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