<?php readfile("./header.txt"); // Header ?>

<?php
//==============================================================================
// RHUL Survey Project - PHP/MySQL Survey Respondent Tracking System
// By Jamie Alnasir, 04/2015
// Royal Holloway University of London
// Dept. Computer Science for Economics Department
// Copyright (c) 2015 Jamie J. Alnasir, All Rights Reserved
//==============================================================================
// Version: PHP edition
//==============================================================================

include_once "../wp-content/themes/choose-survey/const.inc.php";
include_once "../wp-content/themes/choose-survey/db_conn.php";
include_once "../wp-content/themes/choose-survey/surveyappuser.class.php";
include_once "./const.inc.php";


// Acquire DB connection ($db_conn) from db_conn.php
$SurveyApp = new SurveyAppUser($db_conn);

// Get params
//$Survey_Points = htmlspecialchars($_GET["srv_pts"]);

?>

<font size="1">
<ul id="sddm">
    <li><a href='./new-notification.php'>Send new Notification</a></li>
</ul>
</font>
<br><br>

<?php

echo $SurveyApp->getGCMMessageListAdmin(false);
echo "<br>";

echo "</form><br>";
echo "Take me <b><a class='btn' href='" . $Admin_Home . "/metadata.php'>Back</a></b><br>";
echo "<br>";
?>
