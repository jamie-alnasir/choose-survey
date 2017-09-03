
<?php readfile("./header.txt"); // Header ?>

<b><a href="/messaging/new-msg.php">Post a new message!</a></b>

<br><br>
<font face="courier new, courier, ms sans serif">
<b>Output from Choose Survey Syncronisation System:</b><br><br>

<pre>
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

echo exec('/srv/www/survey/sync.123/do-sync-singleton.sh'); 

?>
</pre>

</font>
