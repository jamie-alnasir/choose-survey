
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
$Student_ID = htmlspecialchars($_GET["stu_id"]);

$RHUL_Stu_FirstName = $SurveyApp->getRHUL_LDAP_FieldValue($Student_ID, "display_name");


?>

<table width="80%" cellpadding="5">
<tr>
<td width="33%" bgcolor="#FFB84D">
<b>Choose-Survey metadata for student: </b><br><br>
</td>
</tr>
</table>

<br><br>

<b>Completed surveys</b><br><br>
<?php

echo $SurveyApp->getCompletedSurveyListAdmin( $Student_ID );

?>

