<html>

<script src="/choose-admin/textarea.js"></script>

<body>

<?php readfile("./header.txt"); // Header ?>

<form action="metadata-savescheme.php" method="POST">
Short Name: <input type="text" name="title" size='15' value="shortname"><br><br>
Display Name: <input type="text" name="display_name" size='30' value="display name"><font size=2><b> (what students see!)</b></font><br><br>
Scheme Type: 
<select name="type" id="type">
<option value="R">Recurring</option>
<option value="O">One Off</option>
</select><br>
<br>
Award vouchers by:
<select name="award_by" id="award_by">
<option value="surveys">Surveys</option>
<option value="points">Points</option>
</select> at/every <input type="text" name="voucher_at" size='5' value="3">
<input type="checkbox" name="start_at_one" value="Y"> Start at 1<br><br>
Vouchers/Scheme Expires on: <input type="text" name="expiry_date" size='15' value=""><font size=2><b> (Use DD/MM/YYYY or blank for current date + 6 weeks)</b></font><br>
<br>
Terms: <font size=2><b>(include a voucher value but not expiry)</b></font><br>
<textarea cols="60" rows="10" id="terms" name="terms">
Terms
</textarea>

<br><br>
<font size="1">
<ul id="sddm">
    <li><a href="#" onclick="insertAtCaret('terms','&#163;');return false;">Insert &#163; sign</a></li>
</ul>
</font>


<br><br>
<input type="submit" value="Save">


</form>

</body>
</html>
