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
$Report_ID = htmlspecialchars($_GET["id"]);
if (isset($_GET["sort"]))
{
	$Sort_Col = $_GET["sort"];
} else
{
	$Sort_Col = "";
}

?>

<h3>Choose-survey reports</h3>

<?php

echo $SurveyApp->execChooseReport($Report_ID, 500, $Sort_Col);
echo "<br><br>";
echo "Take me <b><a class='btn' href='" . $Admin_Home . "/report-list.php'>Back</a></b><br>";
echo "<br>";


?>
