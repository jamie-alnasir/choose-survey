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

$Survey_Title = $SurveyApp->getSurveyTitle( $_GET['srv_id'] );

// Find current Survey Target year
$Survey_Target_Yr = $SurveyApp->getSurveyTargetYear( $_GET['srv_id'] );

?>

<h3>Set a Choose-survey's target year</h3>

This restricts the survey to student cohort who's course started in the target year<br>
<br>
Survey: <b><?php echo $Survey_Title?></b><br><br>
Current Survey Target Year: <?php echo $Survey_Target_Yr; ?><br>
<form action="metadata-setsurveyyear.php" method="POST">
<input type="hidden" name="srv_id" value="<?php echo $_GET['srv_id'] ?>">
New Survey Target Year:
<select name='target_year' value='<?php echo $Survey_Target_Yr; ?>'>
<option value='ALL'>ALL</option><option value='2013'>2013</option><option value='2014'>2014</option><option value='2015'>2015</option><option value='2016'>2016</option><option value='2017'>2017</option><option value='2018'>2018</option><option value='2019'>2019</option><option value='2020'>2020</option>   
</select>

<br><br>

<input type="submit" value="Save (Update)">
</form>

</body>
</html>
