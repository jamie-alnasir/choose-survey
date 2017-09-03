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

if( isset($_POST['surveys_vis']) )
{
	$surveys_checked = $_POST['surveys_vis'];
} else
{
	$surveys_checked = None;
}

$SurveyApp->updateSurveyVisibility($surveys_checked);

	echo "Survey visibility set!</b><br><br>";
	echo "Take me <b><a href='" . $Admin_Home . "/metadata-surveylist.php'>Home</a></b>";

?>
