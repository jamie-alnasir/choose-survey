<html>

<script src="/choose-admin/textarea.js"></script>

<body>

<?php readfile("./header.txt"); // Header ?>

<img src="./Google.jpg">
<h3>Choose Survey Push notifications system (via Google)</h3>

NB: Notification message body should not be too long!
<br><br>

<form action="post-notification.php" method="POST">
ose Survey Push notifications system (via Google)</h3>
Notification Title: <input type="text" name="subject" size='20' value="Choose news!" readonly="true"><br><br>

Notification message body: <br>
<textarea cols="40" rows="3" id="content" name="content">This is the place to add the message content.</textarea>
<font size="1">
</font>


<br><br>
<input type="submit" value="Post to all devices">


</form>

</body>
</html>
