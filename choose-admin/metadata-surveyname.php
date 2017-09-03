<html>

<script src="/messaging/textarea.js"></script>

<body>

<?php

readfile("./header.txt"); // Header

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

// Load the SurveyTitle to be edited 
$Survey_Title = $SurveyApp->getSurveyTitle( $_GET['srv_id'] );

?>

<h3>Set a Choose-surveys title</h3>

This overrides the name in the surveymonkey account.<br>
<font size=2>NB: Its not a bad idea to prefix name with a number for sorting i.e. 1. Background</font><br>
<br>
Old Survey Title: <?php echo $Survey_Title; ?><br>
<form action="metadata-setsurveyname.php" method="POST">
<input type="hidden" name="srv_id" value="<?php echo $_GET['srv_id'] ?>">
New Survey Title: <input type="text" name="title" size='50' value="<?php echo $Survey_Title; ?>"><br><br>

<br><br>

<input type="submit" value="Save (Update)">
</form>

</body>
</html>
