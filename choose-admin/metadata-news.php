
<?php

include_once "./const.inc.php"; // Admin constants
readfile("./header.txt"); // Header 

?>


<font size="1">
<ul id="sddm">
    <li><?php echo "<a href='" . $Admin_Home . "/new-msg.php'>Post a new message</a>"; ?></li>
</ul>
</font>

<br><br>
<b>Current RHUL Choose-Survey news messages:</b><br><br>

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

// Acquire DB connection ($db_conn) from db_conn.php
$SurveyApp = new SurveyAppUser($db_conn);


echo $SurveyApp->getSurveyMessageListAdmin();


?>
