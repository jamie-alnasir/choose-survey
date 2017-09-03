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

$title = htmlspecialchars($_POST["title"]);
$displayname = htmlspecialchars($_POST["display_name"]);
$type = htmlspecialchars($_POST["type"]);

if ( isset($_POST[""]))
{
	$start_at_one = htmlspecialchars($_POST["start_at_one"]);
} else
{
	$start_at_one = "";
}

$terms = htmlspecialchars($_POST["terms"]);
$expiry = htmlspecialchars($_POST["expiry_date"]);

$award_at = htmlspecialchars($_POST["voucher_at"]);

$survey_occur = null;
$point_occur = null;

if (htmlspecialchars($_POST["award_by"]) == "surveys") { $survey_occur = $_POST["voucher_at"]; } else { $point_occur = $_POST["voucher_at"]; }


if ($SurveyApp->postNewAutoRewardsScheme($title, $displayname, $type, $survey_occur, $point_occur, $start_at_one, $expiry, $terms) == 0)
{
	echo "<b>New Choose survey QR voucher scheme created!</b><br><br>";
	echo "Take me <b><a href='" . $Admin_Home . "'>Home</a></b>";
} else
{
	echo "<b>failed to save/build the new scheme! - Consult a systems admin or programmer!</b>";
}

?>
