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


# Register 1000 fake devices
#for ($i; $i <= 1000; $i++)
#{
#	$SurveyApp->logGCM_Reg_ID("192.168.1." .$i, "ytfbYFDCBTsyudcfYTSDFBYScfysdbsdcAS." . $i);
#}


?>
