<html>

<?php readfile("./header.txt"); // Header ?>

<h3>Wordpress/RHUL user account mapping</h3>

<body>

<font face="Courier New, Tahoma" size=1>
<?php

// Specially generated report to show WP-Choose Survey and RHUL user account LDAP mappings

include_once "db_conn_wp.php";

$WpUser = new wpUser($db_conn);

$WpUser->MySQL_Table_to_HTML_SQL("select user_id, meta_key, meta_value from cswp_usermeta where meta_key in ('adi_displayname', 'adi_studentno', 'adi_samaccountname', 'adi_mail') order by user_id", 500, explode(",", "user_id,meta_key,meta_value"), "");

class wpUser
{

	private $_db_conn = null;

public function __construct($DB_connection)
	// Constructor
	{
		$this->_db_conn = $DB_connection;
	}


public function MySQL_Table_to_HTML_SQL($sql, $width, $cols, $sort_col)
{

	$result = $this->_db_conn->query($sql);
	
 	if (!$result) return "";

        if ($result->num_rows > 0) {

	echo "<table width=" . $width . ">";

	if ($cols)
	{
		echo "<tr>";
		foreach($cols as $coltitle)
		{
			echo "<td><b>" . $coltitle . "</b></td>";
		}
		echo "</tr>";
	}

	while($row = $result->fetch_assoc())
	{
    	$rownum = 0;
	    echo "<tr>";
   	 foreach($row as $value)
   	 {	
		if ($rownum == 0)
		{
 		       	echo "<td>".$value."</td>";
		} else
		{
			 echo "<td><b>".$value."<b></td>";
		}
   	 }
 	 echo "</tr>\n";
	}
	}

echo "</table>";

}

} // class

?>
</font>

</body>
</html>
