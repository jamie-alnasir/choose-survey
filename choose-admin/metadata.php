
<?php readfile("./header.txt"); // Header ?>

<script>

$Admin_Home = '/choose-admin';

function isValidStuID($aStuID)
{
	return ($aStuID.length == 9) && !(isNaN($aStuID));
}

function isValidPoints(pts)
{
	if (pts > 100) { alert("Value too high, it's not the Zimbabwean dollar!"); }
	if (pts < 0) { alert("Negative points is a bit harsh!"); }
	return (pts > 0) && (pts <= 100);
}

function isValidEmail(email)
{
	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	return re.test(email);
}

function doStudentMetadata()
{
	$student_id = document.getElementById('stu_id').value;

	if (isValidStuID($student_id))
	{
		window.location.assign($Admin_Home + '/metadata-student.php?stu_id=' + $student_id);
	} else
	{
		alert("Student ID entered is not valid!\n\nIt should be 9 digits in length and contain nothing but digits!");
	}

}

function doSurveyPoints()
{
        $srv_pts = document.getElementById('srv_pts').value;
	srv_pts = parseInt($srv_pts);

	if (isValidPoints(srv_pts))
	{ 
		window.location.assign($Admin_Home + '/metadata-setpoints.php?srv_pts=' + $srv_pts);
	} else
	{ 
		alert("Points value not saved! Please enter a valid points value (1 - 100)!");
		document.getElementById('srv_pts').value = '10';
	};
}

function doContactEmail()
{
	$contact_eml = document.getElementById('contact_eml').value;
	if (isValidEmail($contact_eml))
	{
		 window.location.assign($Admin_Home + '/metadata-setmail.php?contact_eml=' + $contact_eml);
	} else
	{
		alert("Email address not saved! Please enter a valid email address!");
		document.getElementById('contact_eml').value = 'economics@rhul.ac.uk';
	}

}

function doLotteryDate(bPrevious)
// bPrevious = true sets the previous lottery date
{

	if (bPrevious) {
		$lot_date = document.getElementById('prev_lot_date').value;
                window.location.assign($Admin_Home + '/metadata-setlotdate.php?lot_date=' + $lot_date + '&lot_type=prev');
	} else
	{
		$lot_date = document.getElementById('lot_date').value;
        	window.location.assign($Admin_Home + '/metadata-setlotdate.php?lot_date=' + $lot_date + '&lot_type=next');
	}
}



</script>

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
$Pts_per_survey = $SurveyApp->getGlobalParam("pts_per_survey");
$Contact_email  = $SurveyApp->getGlobalParam("contact_email");
$Prize1         = $SurveyApp->getGlobalParam("prize1");
$Prize2         = $SurveyApp->getGlobalParam("prize2");
$LAST_SYNC_DATE = $SurveyApp->getGlobalParam("last_sync_date");
$Next_Lottery_Date   = $SurveyApp->getGlobalParam("lottery_date");
$Prev_Lottery_Date   = $SurveyApp->getGlobalParam("prev_lottery_date");

?>

<h3>Surveys and Points</h3>

<table width= 80% cellpadding="5">
<tr>

<td width="33%" bgcolor="#A9E9A9" valign="top">
<b>Surveys</b><br>
Use this to set survey visibility and to activate any "queued" surveys<br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./metadata-surveylist.php">Surveys</a></li>
</ul>
</font>
</td>

<td width="33%" bgcolor="#85CEE6" valign="top">
<b>Survey News</b><br>
Use this to post/edit/delete Choose-Survey project news<br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./metadata-news.php">Administer News</a></li>
</ul>
</font>
</td>

</tr>
<tr>


<td width="33%" bgcolor="#5CBD5C" valign="top">
<b>Current Points per survey</b><br>
Use this to set the current awarded points for completing a survey<br>
<br>
Points-per-survey: <input value="<?php echo $Pts_per_survey; ?>" name="srv_pts" id="srv_pts"><br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="#" onclick="doSurveyPoints();">Save points value</a></li>
</ul>
</font>
</td>

<td width="33%" bgcolor="#FFFF88" valign="top">
<b>Contact email address</b><br>
Use this to set the email to recieve contact messages from the app<br>
<br>
Email: <input value="<?php echo $Contact_email; ?>" name="contact_eml" id="contact_eml"><br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="#" onclick="doContactEmail();">Save</a></li>
</ul>
</font>
</td>

</tr>
<tr>

<td width="33%" bgcolor="#FFB84D" valign="top">
<b>lottery date</b><br>
Use this to set the date that the student will see for the next lottery, use dd/mm/yyyy format. The previous date is used to calculate allocation of lottery tickets from CHOOSE points from that past date onwards<br>
<br>
Upcoming lottery date: <input value="<?php echo $Next_Lottery_Date; ?>" name="lot_date" id="lot_date"><br>
<br>
Previous lottery date: <input value="<?php echo $Prev_Lottery_Date; ?>" name="prev_lot_date" id="prev_lot_date"><br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="#" onclick="doLotteryDate(false);">Set NEXT lottery date</a></li>
    <li><a href="#" onclick="doLotteryDate(true); ">Set PREVIOUS lottery date</a></li>
</ul>
</font>
</td>

<td width="33%" bgcolor="#DDDDDD" valign="top">
<b>Lottery images</b><br>
Use this to upload lottery prize images displayed to the user.<br>
<br>
<img src='/img/prize1.jpg' width=160 height=110 title='1st lottery prize image: <?php echo $Prize1; ?>'>
<img src='/img/prize2.jpg' width=160 height=110 title='2nd lottery prize image: <?php echo $Prize2; ?>'>
<br><br>
<form id="frmImage" action="./metadata-setprizeimage.php" method="post" enctype="multipart/form-data">
<font size="1">
<input type="file" name="file1" id="file1">
<input type="file" name="file2" id="file2">
<br><br>
<ul id="sddm">
    <li><a href="#" onclick="frmImage.submit();">Upload/Set Image(s)</a></li>
</ul>
</font>
</form>
<form id="frmPrizeText" action="./metadata-setprizetext.php" method="get" enctype="multipart/form-data">
<br>
Prize text:<br>
<table border=0>
<tr>
<td width=34%>1st Prize: </td><td><input value="<?php echo $Prize1; ?>" name="prize1" id="prize1"></td>
</tr><tr>
<td width=34%>2nd Prize: </td><td><input value="<?php echo $Prize2; ?>" name="prize2" id="prize2"></td>
</tr>
</table>
<br>
<ul id="sddm">
    <li><a href="#" onclick="frmPrizeText.submit();">Set Prize Text</a></li>
</ul>
</form>
</td>


</td>
</tr>
<tr>
<td width="33%" bgcolor="#85CEE6" valign="top" colspan=2>
<b>Choose-Survey Reports</b><br>
Use this to view reports directly from the database current as of last sync: <font color="green"><?php echo $LAST_SYNC_DATE; ?></font><br>
<font size=2>NB: Sync date/time will be SurveyMonkey API server date/time, if US then GMT-timezone_difference</font><br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./report-list.php">Reports</a></li>
</ul>
</font>
</td>

</tr>
<tr>

</table>

<h3>Push notification system</h3>

<table width= 80% cellpadding="5">
<tr>

<td width="33%" bgcolor="#A9E9A9" valign="top" colspan=2>
<b>Choose-survey Push notifications</b><br>
Use this to deliver push notifications to choose-survey mobile app users<br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./new-notification.php">Push a notification</a></li>
    <li><a href="./metadata-pushdevlist.php">View registered devices</a></li>
    <li><a href="./metadata-pushmsglist.php">View pushed message log</a></a>
</ul>
</font>
</td>
</tr>
</table>

<h3>QR code Voucher system</h3>

<table width= 80% cellpadding="5">
<tr>

<td width="33%" bgcolor="#5CBD5C" valign="top">
<b>View active Vouchers list</b><br>
Use this to view all the vouchers in circulation and their fastcodes generated for student completed surveys<br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./metadata-voucherlist.php">Show list</a></li>
</ul>
</font>
</td>

<td width="33%" bgcolor="#FFFF88" valign="top">
<b>Redeem vouchers</b><br>
Use this to redeem Choose QR voucher (withdraw from circulation)<br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./metadata-redeem.php">Reedeem voucher(s)</a></li>
</ul>
</font>
</td>

<tr>

<td width="33%" bgcolor="#A9E9A9" valign="top" colspan=2>
<b>Choose-survey voucher schemes</b><br>
Use this to view all the voucher fastcodes generated for student completed surveys<br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="./metadata-schemelist.php">Administer Schemes</a></li>
</ul>
</font>
</td>



</tr></table>

<h3>Respondent meta-data</h3>

<table width= 80% cellpadding="5">
<tr>

<td width="33%" bgcolor="#D69999" valign="top">
<b>Delete ALL responses for ALL surveys</b><br>
Use this when all responses have first been deleted from the Surveymonkey account.<br>
<br>
<font size="1">
<ul id="sddm">
    <li><?php echo "<a href='" . $Admin_Home . "/metadata-delete-responses.php'>Delete ALL metadata</a>"; ?></li>
</ul>
</font>
</td>

<td width="33%" bgcolor="#FFB84D">
<b>Clear a students survey response</b><br>
Use this to clear a students response to a survey.<br>
NB: The response must also be first cleared in Surveymonkey<br>
<br>
<b>Pick the students ID to administer</b>
<br>
Student ID: <input text="" name="stu_id" id="stu_id"><br>
<br>
<font size="1">
<ul id="sddm">
    <li><a href="#" onclick="doStudentMetadata();">List surveys for this student</a></li>
</ul>
</font>
</td>
</tr>

<tr>
<td colspan=2 bgcolor="#FFFF88">
<b>Clear ALL Responses for a particular survey</b><br>
Use this to clear ALL responses for a specific survey.<br>
NB: The responses must also be first cleared in Surveymonkey<br>
<br>
<table width="100%" bgcolor="#EEEEEE" border="1">
<tr>
<td>
<b>RHUL Choose Surveys</b><br><br>
<?php

	echo $SurveyApp->getSurveyListAdmin(true);

?>
</td>
</tr>
</table>
</td>

</tr>
</table>


