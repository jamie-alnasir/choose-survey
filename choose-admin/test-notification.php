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
include_once "../push.123/rhul-push.php";
include_once "./const.inc.php";

$GCM = new RHULGCMPushEngine($db_conn);

?><h3>TEST SCRIPT -- Choose Survey Push notifications system (via Google)</h3><?php

if ($GCM->pushSingle(htmlspecialchars($_POST["gcm_id"]), htmlspecialchars($_POST["subject"]), htmlspecialchars($_POST['content'])) == 0)
{
	echo "<br>";
	echo "<b>Notification despatched!</b><br><br>";
	echo "<b>Subject: </b>" . htmlspecialchars($_POST["subject"]) . "<br><br>";
	echo "<b>Content: </b><br>" . nl2br(htmlspecialchars($_POST["content"])) . "<br><br>";
	echo "Take me <b><a href='" . $Admin_Home . "'>Home</a></b>";
} else
{
	echo "<b>failed to post the new message! - Consult a systems admin or programmer!</b>";
}

?>
