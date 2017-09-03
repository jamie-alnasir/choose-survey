
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

// Get params
$Debug_Msg = htmlspecialchars($_GET["msg"]);

?>

<h3>Debug message viewer</h3>

<?php

echo $Debug_Msg;
echo "<br>";


?>
