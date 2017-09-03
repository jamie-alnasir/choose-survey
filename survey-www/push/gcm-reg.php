<?php
//==============================================================================
// RHUL Survey Project - PHP/MySQL Survey Respondent Tracking System
// Google cloud messenger Registration ID DB logger
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


$reg_id = "";
$ip_addr = "";
$RESULT_REGISTERED = "REGISTERED"; // if db registration successfull (only for first registration)
$RESULT_ERROR = "ERROR"; // if no reg_id supplied
$RESULT_NULL = "NULL"; // if db registration not successful (on subsequent attempts), no distinction with db error

if (isset($_SERVER['REMOTE_ADDR']))
{
	$ip_addr = $_SERVER['REMOTE_ADDR'];
} else
{
	$ip_addr = "unresolved";
}

# Register GCM registration key with RHUL server
if (isset($_GET['reg_id']))
{
	$reg_id = $_GET['reg_id'];
	if ($SurveyApp->logGCM_Reg_ID($ip_addr, $reg_id) != -1)
	{
		echo $RESULT_REGISTERED;
		// Returned the first time a device's reg_id is successfully logged
		// subsequent registration attempts result in NULL
	} else
	{
		echo $RESULT_NULL;
	}
} else
{
	echo $RESULT_ERROR;
}


?>
