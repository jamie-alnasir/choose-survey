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
$Prize1 = htmlspecialchars($_GET["prize1"]);
$Prize2 = htmlspecialchars($_GET["prize2"]);

if ($SurveyApp->updateGlobalParam("prize1", $Prize1) == 0)
{
        echo "<b>1st Prize text (" . $Prize1 . ") updated in the RHUL metadata!</b><br><br>";
} else
{
        echo "<b>Failed to update the metadata! - Consult a systems admin or programmer!</b>";
}

echo "<br>";

if ($SurveyApp->updateGlobalParam("prize2", $Prize2) == 0)
{
        echo "<b>2nd Prize text (" . $Prize2 . ") updated in the RHUL metadata!</b><br><br>";
} else
{
        echo "<b>Failed to update the metadata! - Consult a systems admin or programmer!</b>";
}


echo "Take me <b><a class='btn' href='" . $Admin_Home . "/metadata.php'>Back</a></b>";


?>
