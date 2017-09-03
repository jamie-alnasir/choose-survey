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

    // NB: This is the Wordpress DB connection object!


    // Include site constants
    include_once "const.inc.php";

    // Set the error reporting level
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    // Start a PHP session
    session_start();
	
	
	// Create connection
	$db_conn = new mysqli($wp_servername, $wp_username, $wp_password, $wp_dbname);
	// Check connection
	if ($db_conn->connect_error) {
		die("Connection failed: " . $db_conn->connect_error);
	}
	
?>

