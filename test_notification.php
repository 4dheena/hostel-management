<?php

require_once 'database/db_connect.php';
require_once 'database/notify.php';

createNotification(
$conn,
1,
1,
"Test Notification",
"This is a test notification.",
"test",
NULL
);

echo "Notification inserted.";

?>