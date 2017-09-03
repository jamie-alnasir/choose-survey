<html>

<script src="/choose-admin/textarea.js"></script>

<body>

<?php readfile("./header.txt"); // Header ?>

<form action="post-msg.php" method="POST">
Subject: <input type="text" name="subject" size='50' value="subject goes here!"><br><br>

Message body: <br>
<textarea cols="60" rows="10" id="content" name="content">
This is the place to add the message content.
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
