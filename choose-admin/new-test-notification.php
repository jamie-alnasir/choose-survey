<html>

<script src="/choose-admin/textarea.js"></script>

<body>

<?php readfile("./header.txt"); // Header ?>

<img src="./Google.jpg">
<h3><font color=red>TEST - Choose Survey Push notifications system (via Google)</font></h3>

NB: Notification message body should not be too long!
<br><br>

<form action="test-notification.php" method="POST">
<h3>Choose Survey Push notifications system (via Google)</h3>
Notification Title: <input type="text" name="subject" size='20' value="Choose news!"><br><br>

Test Target device (GCM-Token): <input type="text" name="gcm_id" size='120' value="<GCM_ID here>"><br><br>

Notification message body: <br>
<textarea cols="40" rows="3" id="content" name="content">Debug message!</textarea>
<font size="1">
</font>


<br><br>
<input type="submit" value="Post to single device">


</form>

</body>
</html>
