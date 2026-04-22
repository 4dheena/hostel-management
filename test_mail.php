<?php

require_once 'utils/send_email.php';

$to = "anaghaminnu681@gmail.com"; // where you want to receive
$subject = "Hostel System Test Email";

$message = "
<h3>Test Email Successful 🎉</h3>
<p>Your Hostel Management System email setup is working correctly.</p>
<p>This email was sent using PHPMailer + Gmail SMTP.</p>
";

sendEmail($to, $subject, $message);

echo "Email function executed.";

?>