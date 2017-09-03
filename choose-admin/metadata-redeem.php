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
//$Survey_Points = htmlspecialchars($_GET["srv_pts"]);

?>

<form action="./metadata-redeemlist.php" method="POST">

<h3>Redeem Choose QR voucher(s)</h3>

List of vouchers (each on it's own line, no commas): <br><br>
<font size=2>Use voucher code, i.e. <b><b>voucher_ge8yr6h31j7mdbo9u007</b> or fastcode, i.e. <b>17709</b> (variable length)<br></font>
<br>
<textarea cols="60" rows="10" id="voucher_list" name="voucher_list">
</textarea>
<br>
<input type="submit" value="Redeem (remove from circulation)">
</form>
