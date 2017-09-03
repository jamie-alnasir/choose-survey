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
$Lot_Date = htmlspecialchars($_GET["lot_date"]);
$Lot_Date_Type = htmlspecialchars($_GET["lot_type"]);
$lot_param = "lottery_date";

if ($Lot_Date_Type == "prev") {
	$lot_param = "prev_lottery_date";
	$lot_msg   = "Previous lottery date: ";
} else
{
	$lot_param = "lottery_date";
	$lot_msg   = "Next lottery date: ";
}

if ($SurveyApp->updateGlobalParam($lot_param, $Lot_Date) == 0)
{
        echo "<b>" . $lot_msg . $Lot_Date . " updated in the RHUL metadata!</b><br><br>";
        echo "Take me <b><a class='btn' href='" . $Admin_Home . "/metadata.php'>Back</a></b>";
} else
{
        echo "<b>Failed to update the metadata! - Consult a systems admin or programmer!</b>";
}


?>
