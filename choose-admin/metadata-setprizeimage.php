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
//$Lot_Date = htmlspecialchars($_GET["file1"]);
//$Lot_Date_Type = htmlspecialchars($_GET["lot_type"]);
//$lot_param = "lottery_date";

$allowedExts = array("jpg", "png", "gif", "jpeg");

//var_dump($_FILES);

// If uploaded, process file1 (1st Prize picture)
if ($_FILES["file1"]["name"] != "")
{
echo "<h3>Processing upload of 1st prize picture</h3>";
$temp = explode(".", $_FILES["file1"]["name"]);
$extension = end($temp);
if ( ($_FILES["file1"]["size"] < 1000000) && in_array($extension, $allowedExts))
{
  if ($_FILES["file1"]["error"] > 0)
    {
    echo "An Error occurred: " . $_FILES["file1"]["error"] . "<br>";
    }
  else
    {
    $FilePath = $_FILES["file1"]["tmp_name"];	
    $FileName = $_FILES["file1"]["name"];
    echo "The file has been uploaded into temporary file: " . $FilePath . "<br>";

    echo "Original filename on client upload: " . $FileName . "<br>";
    echo "File type: " . $_FILES["file1"]["type"] . "<br>";
    echo "File size: " . ($_FILES["file1"]["size"] / 1024) . " kB<br>";
    echo "<br>";

    if (copy($FilePath, "/srv/www/survey/img/prize1.jpg")) {
        echo "Done. The first prize image has been updated in the system.";
    } else {
        echo "An error occurred. The first prize image has probably not been updated!";
    }

    }
  }
else
  {
	if ($_FILES["file1"]["size"] > 1000000)
	{
	  echo "<img src='./msg-error.jpg'> <b>File too large (> 1Mb)</b><br>";
	} else
	{
	  echo "<img src='./msg-error.jpg'> <b>Invalid file submitted</b><br>";
	  echo "<br>The file should meet the following requirements:";
	  echo "<ul>";
	  echo "<li>must be of .jpg file extension</li>";
	  echo "<li>must not exceed ~1mb</li>";
	  echo "</ul>";
	  echo "<b>Please use the 'back' button on your browser to select another file!</b>";
	}
  }
}

// If uploaded, process file2 (2nd Prize picture)
if ($_FILES["file2"]["name"] != "")
{
echo "<h3>Processing upload of 2nd prize picture</h3>";
$temp = explode(".", $_FILES["file2"]["name"]);
$extension = end($temp);
if ( ($_FILES["file2"]["size"] < 1000000) && in_array($extension, $allowedExts))
{
  if ($_FILES["file2"]["error"] > 0)
    {
    echo "An Error occurred: " . $_FILES["file2"]["error"] . "<br>";
    }
  else
    {
    $FilePath = $_FILES["file2"]["tmp_name"];
    $FileName = $_FILES["file2"]["name"];
    echo "The file has been uploaded into temporary file: " . $FilePath . "<br>";

    echo "Original filename on client upload: " . $FileName . "<br>";
    echo "File type: " . $_FILES["file2"]["type"] . "<br>";
    echo "File size: " . ($_FILES["file2"]["size"] / 1024) . " kB<br>";
    echo "<br>";
    
    if (copy($FilePath, "/srv/www/survey/img/prize2.jpg")) {
        echo "Done. The second prize image has been updated in the system.";
    } else {
        echo "An error occurred. The second prize image has probably not been updated!";
    }

    }
  }
else
  {
        if ($_FILES["file2"]["size"] > 1000000)
        {
          echo "<img src='./msg-error.jpg'> <b>File too large (> 1Mb)</b><br>";
        } else
        {
          echo "<img src='./msg-error.jpg'> <b>Invalid file submitted</b><br>";
          echo "<br>The file should meet the following requirements:";
          echo "<ul>";
          echo "<li>must be of .jpg file extension</li>";
          echo "<li>must not exceed ~1mb</li>";
          echo "</ul>";
          echo "<b>Please use the 'back' button on your browser to select another file!</b>";
        }
  }
}



?>
