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

// Load the message to be edited  
$Survey_Msg = $SurveyApp->getSurveyMessage( $_GET['msg_id'] );
$Survey_Msg[1] = $SurveyApp->RHUL_TokenFromHTML( str_replace('<br>', "", $Survey_Msg[1]) );

?>

<form action="update-msg.php" method="POST">
<input type="hidden" name="msg_id" value="<?php echo $_GET['msg_id'] ?>">
Subject: <input type="text" name="subject" size='50' value="<?php echo $Survey_Msg[0]; ?>"><br><br>

Message body: <br>
<textarea cols="60" rows="10" id="content" name="content">
<?php echo $Survey_Msg[1]; ?>
</textarea>

<br><br>
<font size="1">
<ul id="sddm">
    <li><a href="#" onclick="insertAtCaret('content','[url=\'http://mylink.com\']link title[/url]');return false;">Insert URL</a></li>
    <li><a href="#" onclick="insertAtCaret('content','[img=\'https://www.royalholloway.ac.uk/SiteElements/Images/royalhollowaylogo.png\']');return false;">Insert Image</a></li>
</ul>
</font>

<br><br>

<input type="submit" value="Save (Update)">
</form>

</body>
</html>
