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

include 'cipher.php';


function TD($cell) { return "<td>" . $cell . "</td>"; }
function TR($cell) { return "<tr>" . $cell . "</tr>"; }
function B($cell) { return "<b>" . $cell . "</b>"; }


class SurveyAppUser
{

	private $_db_conn = null;
	public $HTML_IMG_Tick = "<img src='/img/tick.png' alt='This action is completed' title='This action is complete'>";
	public $HTML_IMG_Arrow = "<img src='/img/arrow.png' alt='This action is not yet completed' title='This action is not yet complete'>";
	public $HTML_IMG_Edit = "<img src='/img/edit-icon.png' alt='Edit item' title='Edit item'>";
	public $HTML_IMG_Delete = "<img src='/img/delete-icon.png' alt='Delete item' title='Delete item'>";
	public $HTML_IMG_Warn = "<img src='/img/warning.png' title='Warning!'>";
	public $HTML_IMG_Debug = "<img src='/img/debug-icon.png' title='Not a real bug!'>";
	public $HTML_Zero_Completed_Survey_Result = "<h1>0</h1>";
	public $HTML_Zero_UnCompleted_Survey_Result = "<h1>0</h1>";
	public $MAX_MSGS = 3;
	public $MAX_COMPLETED = 3;
	public $MAX_UNCOMPLETED = 10;
	public $DEBUG_MODE = 0; // Set to 1 to Enable, 0 to Disable
	public $REPORT_SQL = 1; // Set to 1 to Enable, 0 to Disable
	public $DEBUG_MSG_SHOW_URL = "/choose-admin/debug-viewmsg.php";
	public $CIPHER     = 0; // As above
	public $HTML_ADMIN_URL = "/choose-admin/"; // Include trailing /
	public $EML_CHOOSE = "jamie.a@al-nasir.com"; // used if destination lookup fails
	public $VOUCHER_WEB_FOLDER = "/choose-vouchers/";
	public $VOUCHER_WEB_VIEW = "/voucher-view/?v_id=";
	public $SURVEY_SAS_SYNC_FOLDER = "/home/mxba001/RHUL_Survey_SAS/";
	public $VOUCHER_PURGE_FILE = "/home/mxba001/RHUL_Survey_SAS/VOUCHER_PURGE.txt";
	public $VOUCHER_PURGE_SCRIPT = "/home/mxba001/RHUL_Survey_SAS/purge-stale-vouchers.sh";

	public function __construct($DB_connection)
	// Constructor
	{
		$this->_db_conn = $DB_connection;
		if ($this->DEBUG_MODE)
		{
			echo $this->HTML_IMG_Warn . "<font size=2><b> Debug mode is enabled. This is not an error, the system is being maintained/tested. You may see verbose diagnostic messages. You may see bugs, they are not real! Dont't panic! Work as usual.</b></font><hr size=1>";
		}
	}
	
	public function _DEBUG_SQL($str)
	{
		if ($this->DEBUG_MODE)
		{
			//echo "<font size=1>" . $str . "</font><br>";
			echo "<a href='" . $this->DEBUG_MSG_SHOW_URL . "?msg=SQL:" . str_replace('\'', '&#39;', $str) . ";'  target='_new_debug_frame'>" . $this->HTML_IMG_Debug . "</a>";
		}
	}
	
	public function RHUL_TokenToHTML($aStr)
	{
		$result = $aStr;

		# URLs
		$result = str_replace("[/url]", "</a>", $result);
		$result = str_replace("[url=", "<a href=", $result);

		# Images
		$result = str_replace("[img=", "<img src=", $result);

		# Cleanup any tags
		$result = str_replace("]", ">", $result);
                $result = str_replace("[","<", $result);

		return $result;
	}

	public function RHUL_TokenFromHTML($aStr)
        {
                $result = $aStr;

		# next lines for clarity only
		$result = str_replace('&lt;', '<', $result);
		$result = str_replace('&gt;', '>', $result);

		$result = str_replace('</a>', '[/url]', $result);
                $result = str_replace("<a href=", "[url=",  $result);

		# Images
                $result = str_replace("<img src=", "[img=", $result);

		# Cleanup any tags
                $result = str_replace("<", "[", $result);
		$result = str_replace(">", "]", $result);		
                return $result;
        }


    public function didConsent($student_id)
    // Return TRUE if given student completed a consent survey
    {

                $sql = "SELECT * FROM vw_rhul_consent WHERE student_id = '" . $student_id . "'";
                $this->_DEBUG_SQL($sql);

                $result = $this->_db_conn->query($sql);

                if ($result->num_rows > 0) {
			return 1;
                } else {
			return 0;
                }

    }

    public function getSurveyList()
	// Obtain list of surveys
    {
	
		$sql = "SELECT survey_id, title FROM tblsurveys WHERE hidden <> 'Y' OR hidden IS NULL";
		$this->_DEBUG_SQL($sql);
		
		$result = $this->_db_conn->query($sql);

		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				echo "survey_id: " . $row["survey_id"]. " - " . $row["title"]. "<br>";
			}
		} else {
			echo "0 results found";
		}
	
    }


	public function getCompletedSurveyList($aStudent_ID)
	// Obtain list of Completed surveys for the given user
    {
		$sql = "SELECT survey_id, title, url FROM tblsurveys"
			 . " WHERE survey_id IN (SELECT survey_id FROM tblsurveys_completed where student_id = '" . $aStudent_ID . "') LIMIT " . $this->MAX_COMPLETED;
		
		$this->_DEBUG_SQL($sql);
		$result = $this->_db_conn->query($sql);

		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {


				if ($this->DEBUG_MODE)
				{
					echo $this->HTML_IMG_Tick . " survey_id: " . $row["survey_id"]. " - " . $row["title"].	"<br>";

				} else
				{
					echo $this->HTML_IMG_Tick . " <b>" . $row["title"]. "</b><br>"; 
				}
				
			}
		} else {
			echo $this->HTML_Zero_Completed_Survey_Result;
		}
    }


	public function getCompletedSurveyListAdmin($aStudent_ID)
	// Obtain list of Completed surveys for the given user
    {
		$sql = "SELECT survey_id, title, url FROM tblsurveys"
			 . " WHERE survey_id IN (SELECT survey_id FROM tblsurveys_completed where student_id = '" . $aStudent_ID . "') ORDER BY title ASC";
		
		$this->_DEBUG_SQL($sql);
		$result = $this->_db_conn->query($sql);

		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {

				$delete_response_url = "<a href='" . $this->HTML_ADMIN_URL  . "/metadata-delete-response.php?stu_id=" . $aStudent_ID . "&srv_id=" . $row["survey_id"] . "'>" . $this->HTML_IMG_Delete . "</a>";


				if ($this->DEBUG_MODE)
				{
					echo $delete_response_url . " survey_id: " . $row["survey_id"]. " - " . $row["title"] .	"<br>";

				} else
				{
					echo $delete_response_url . " <b>" . $row["title"] .  "</b><br>"; 
				}
				
			}
		} else {
			echo $this->HTML_Zero_Completed_Survey_Result;
		}
    }

    public function getSurveyListAdmin($bMetadata)
        // Obtain list of ALL surveys
	// if bMetadata then meta-cata clear/delete icons present, else generates
	// array of checkboxes for survey visibility 
    {

                $sql = "SELECT survey_id, title, url, hidden, target_year FROM tblsurveys ORDER BY title";
                $this->_DEBUG_SQL($sql);
				
				$result = $this->_db_conn->query($sql);

                if ($result->num_rows > 0) {

			if (!$bMetadata) { echo "<table width=600><tr><td><b>Visible</b></td><td><b>Target Year</b></td><td><b>Survey Title</b></td></tr>"; }

                        while($row = $result->fetch_assoc()) {

                                $delete_response_url = "<a href='" . $this->HTML_ADMIN_URL . "metadata-delete-survey-responses.php?srv_id=" . $row["survey_id"] . "'>" . $this->HTML_IMG_Delete . "</a>";


                                if ($this->DEBUG_MODE)
                                {
                                        echo $delete_response_url . " survey_id: " . $row["survey_id"]. " - " . $row["title"].  "<br>";

                                } else
                                {
					if ($bMetadata)
					{
						echo $delete_response_url . " <b>" . $row["title"]. "</b><br>";
					} else
					{
						$title = $row["title"];
		                                $rename_url = "<font size=2>[ <a href='" . $this->HTML_ADMIN_URL . "/metadata-surveyname.php?srv_id=" . $row["survey_id"] . "'>rename</a> ]</font>";
                		                $title = $rename_url . " " . $title;


						if (!$row["hidden"] == "Y") { $val = " checked"; } else { $val = ""; }
						$chkbx = "\n<input type='checkbox' name='surveys_vis[]' value='" . $row["survey_id"] . "'" . $val . ">";

						if ($row["target_year"] == null) { $target_yr_val = "ALL"; } else { $target_yr_val = $row["target_year"]; };
						$target_yr_url = "<font size=2>[ <a href='" . $this->HTML_ADMIN_URL . "/metadata-surveyyear.php?srv_id=" . $row["survey_id"] . "'>set</a> ]</font> " . $target_yr_val;
						echo "<tr><td>" . $chkbx . " <b><td>" . $target_yr_url . "</td></td><td>" . $title . "</b></td></tr>";
					}
                                }
                        }

			if (!$bMetadata) { echo "</table>"; }

                } else {
                        echo $this->HTML_Zero_Completed_Survey_Result;
                }
    }


	public function getUncompletedSurveyList($aStudent_ID, $aStudent_Yr = null)
	// Obtain list of outstanding surveys for the given user
    {

		if ($aStudent_Yr == null)
		{
			$yr = "";
		} else
		{
			$yr = " AND (target_year = '" . $aStudent_Yr . "' OR target_year IS NULL) ";
		}

		if ($this->CIPHER)
		{
		$sql = "SELECT survey_id, title, url FROM tblsurveys"
			 . " WHERE survey_id NOT IN (SELECT survey_id FROM tblsurveys_completed where student_id = '" . RHUL_Cipher( $aStudent_ID ) . "') AND (hidden <> 'Y' OR hidden IS NULL) " . $yr . " ORDER BY title ASC LIMIT " . $this->MAX_UNCOMPLETED;
		} else
		{
$sql = "SELECT survey_id, title, url FROM tblsurveys"
                         . " WHERE survey_id NOT IN (SELECT survey_id FROM tblsurveys_completed where student_id = '" . $aStudent_ID . "') AND (hidden <> 'Y' OR hidden IS NULL) " . $yr . " ORDER BY title ASC LIMIT " .$this->MAX_UNCOMPLETED;
		}

		$this->_DEBUG_SQL($sql);
		$result = $this->_db_conn->query($sql);

		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
		if ($this->CIPHER)
		{
				$trackable_url = "<a href='" . $row["url"] . "?c=" . RHUL_Cipher( $aStudent_ID ) . "'>" . $row["title"] . "</a>";
		} else
		{
				$trackable_url = "<a href='" . $row["url"] . "?c=" . $aStudent_ID  . "'><b>" . $row["title"] . "</b></a>";
		}
				

				if ($this->DEBUG_MODE)
				{
					echo $this->HTML_IMG_Arrow . " survey_id: " . $row["survey_id"]. " - " . $trackable_url . "<br>";

				} else
				{
					echo $this->HTML_IMG_Arrow . " " .  $trackable_url . "<br>";
				}


			}
		} else {
			echo $this->HTML_Zero_UnCompleted_Survey_Result;
		}
    }

   public function getSurveyTitle($aSurvey_ID)
    // Return Survey Title
    {

        $msgResult = "not found!";

        $sql = "SELECT title FROM tblsurveys WHERE survey_id = " . $aSurvey_ID;
        $this->_DEBUG_SQL($sql);
        $result = $this->_db_conn->query($sql);

	if (!$result) { return $msgResult; };

        if ($result->num_rows > 0) {

                        while($row = $result->fetch_assoc()) {
                                $msgResult = $row["title"];
                                return $msgResult;
                        }
                } else {
                        return $msgResult;
                }

    }

public function getSurveyTargetYear($aSurvey_ID)
    // Return Survey Target year
    {

        $msgResult = "not found!";

        $sql = "SELECT target_year FROM tblsurveys WHERE survey_id = " . $aSurvey_ID;
        $this->_DEBUG_SQL($sql);
        $result = $this->_db_conn->query($sql);

        if (!$result) { return $msgResult; };

        if ($result->num_rows > 0) {

                        while($row = $result->fetch_assoc()) {
                                $msgResult = $row["target_year"];
				if ($msgResult == null) { $msgResult = "ALL"; };
                                return $msgResult;
                        }
                } else {
                        return $msgResult;
                }

    }


 public function updateSurveyTitle($aSurvey_ID, $aTitle)
        // Set survey title
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

                $sql = "UPDATE tblsurveys SET title = '" . addslashes($aTitle) ."' WHERE survey_id = " . $aSurvey_ID;
                $this->_DEBUG_SQL($sql);

                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }

 public function updateSurveyYear($aSurvey_ID, $aYear)
        // Set survey target year
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

		if (($aYear == "ALL") || ($aYear == null))
		{
			$sql = "UPDATE tblsurveys SET target_year = NULL WHERE survey_id = " . $aSurvey_ID;
		} else
		{
                	$sql = "UPDATE tblsurveys SET target_year = " . addslashes($aYear) ." WHERE survey_id = " . $aSurvey_ID;
		}

                $this->_DEBUG_SQL($sql);

                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }




    public function postSurveyMessage($aSubject, $aMsgContent)
	// Post a message to RHUL Survey users in the messaging table
	// Must use mysqli_real_escape_string to correctly escape characters and
	// prevent SQL Injection attack, see PHP documentation below:
	// http://php.net/manual/en/security.database.sql-injection.php
    {

		$aMsgContent = str_replace("\n", "<br>\n", $aMsgContent);
		$aMsgContent = $this->RHUL_TokenToHTML($aMsgContent);

		$sql = "INSERT INTO tblsurvey_msgs (subject, body) VALUES ('" . mysqli_real_escape_string($this->_db_conn, $aSubject) . "', '" . mysqli_real_escape_string($this->_db_conn, $aMsgContent) . "')";

		$this->_DEBUG_SQL($sql);
		if ($this->_db_conn->query($sql) === TRUE) {
		    return 0;
		} else {
		    return -1;
		}
    }


    public function getSurveyMessageList()
	// Obtain list of most recent messages from the messaging table
    {
		$sql = "SELECT msg_id, subject FROM tblsurvey_msgs ORDER BY msg_id DESC LIMIT " . $this->MAX_MSGS;
		
		$this->_DEBUG_SQL($sql);
		$result = $this->_db_conn->query($sql);
		
		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				$url = $row["msg_id"];
				$msg_url = "<a href='/?msg_id=" . $url . "'>" . $row["subject"] . "</a>";
				echo $msg_url . "<br>";
			}
		} else {
			echo "0 results found";
		}
		
    }


    public function getSurveyMessageListAdmin()
	// Obtain list of ALL messages from the messaging table
    {
		$sql = "SELECT msg_id, subject FROM tblsurvey_msgs ORDER BY msg_id DESC";
		
		$this->_DEBUG_SQL($sql);
		$result = $this->_db_conn->query($sql);
		
		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				$url = $row["msg_id"];
				$msg_url = "<a href='/?msg_id=" . $url . "' target='_pseudoMsgView'>" . $row["subject"] . "</a>";
				$edit_msg_url = "<a href='" . $this->HTML_ADMIN_URL . "edit-msg.php?msg_id=" . $url . "'>" . $this->HTML_IMG_Edit . "</a>";
				$delete_msg_url = "<a href='" . $this->HTML_ADMIN_URL . "delete-msg.php?msg_id=" . $url . "'>" . $this->HTML_IMG_Delete . "</a>";
				echo $delete_msg_url . " " . $edit_msg_url . " <b>" . $msg_url . "</b><br>";
			}
		} else {
			echo "0 results found";
		}
		
    }


    public function getSurveyMessage($aMsgID)
    // Return message from the messaging table as an Array in the form of Array[subject, content]
    {

	$msgResult = ["not found!", "Requested message does not exist!"];

	$sql = "SELECT msg_id, subject, body FROM tblsurvey_msgs WHERE msg_id = " . $aMsgID;
	$this->_DEBUG_SQL($sql);
	$result = $this->_db_conn->query($sql);

	if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				$msgResult[0] = $row["subject"];
				$msgResult[1] = $row["body"];
				return $msgResult;
			}
		} else {
			return $msgResult;
		}		
		
    }


    public function deleteSurveyMessage($aMsgID)
    // Remove message from the messaging table
    {
	$sql = "DELETE FROM tblsurvey_msgs WHERE msg_id = " . $aMsgID;
	$this->_DEBUG_SQL($sql);
	$result = $this->_db_conn->query($sql);

	if ($this->_db_conn->query($sql) === TRUE) {
	    return 0;
	} else {
	    return -1;
	}
		
    }



	public function updateSurveyMessage($Msg_ID, $aSubject, $aMsgContent)
        // Update a message in the RHUL Survey users in the messaging table
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

                $aMsgContent = str_replace("\n", "<br>\n", $aMsgContent);
		$aMsgContent = $this->RHUL_TokenToHTML($aMsgContent);

                $sql = "UPDATE tblsurvey_msgs SET subject = '" . mysqli_real_escape_string($this->_db_conn, $aSubject) . "', body = '" . mysqli_real_escape_string($this->_db_conn, $aMsgContent) . "' WHERE msg_id = " . $Msg_ID;

				$this->_DEBUG_SQL($sql);
                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }

     public function updateSurveyVisibility($SurveyID_Exclude_List)
        // Update a message in the RHUL Survey users in the messaging table
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

		if ($SurveyID_Exclude_List)
		{
			$exc_field_list = '\'' . implode('\',\'', $SurveyID_Exclude_List) . '\'';

        	        $sql = "UPDATE tblsurveys SET hidden = 'Y' WHERE survey_id NOT IN (" . $exc_field_list . ")";
               		if ($this->_db_conn->query($sql) == TRUE) { $step1 = 0; } else { $step1 = -1; }

                	$sql = "UPDATE tblsurveys SET hidden = NULL WHERE survey_id IN (" . $exc_field_list . ")";
			
			$this->_DEBUG_SQL($sql);
			if ($this->_db_conn->query($sql) == TRUE) { $step2 = 0; } else { $step2 = -1; }

			if ($step1 + $step2 == 0)
			{
				return 0;
			} else
			{
				return -1;
			}

		} else
		{
			$sql = "UPDATE tblsurveys SET hidden = NULL";
			$this->_DEBUG_SQL($sql);
			if ($this->_db_conn->query($sql) == TRUE) { return 0; } else { return -1; }
		}

    }


    public function deleteAllResponses()
    // Remove ALL Repondent data from the completed surveys table
    {
	$sql = "DELETE FROM tblsurveys_completed";
	$this->_DEBUG_SQL($sql);
	$result = $this->_db_conn->query($sql);

	if ($this->_db_conn->query($sql) === TRUE) {
	    return 0;
	} else {
	    return -1;
	}
		
    }


    public function deleteSurveyResponses($aSurveyID)
    // Remove ALL Repondent data from the completed surveys table
    // for a given survey
    {
        $sql = "DELETE FROM tblsurveys_completed WHERE survey_id='" . $aSurveyID . "'";
		$this->_DEBUG_SQL($sql);
        $result = $this->_db_conn->query($sql);

        if ($this->_db_conn->query($sql) === TRUE) {
            return 0;
        } else {
            return -1;
        }

    }


    public function deleteResponse($aStudentID, $aSurveyID)
    // Remove specific survey Repondent data from the completed surveys table
    // for given survey and student
    {
        $sql = "DELETE FROM tblsurveys_completed WHERE student_id='" . $aStudentID . "' AND survey_id='" . $aSurveyID . "'";
        $this->_DEBUG_SQL($sql);
		$result = $this->_db_conn->query($sql);

        if ($this->_db_conn->query($sql) === TRUE) {
            return 0;
        } else {
            return -1;
        }

    }


   public function updateSurveyPoints($Points_per_survey)
        // Update the Points awarded for surveys in the RHUL Survey parameters table
        // http://php.net/manual/en/security.database.sql-injection.php
 {

                $sql = "UPDATE tblsurvey_global SET value = '" . $Points_per_survey ."' WHERE param = 'pts_per_survey'";
                $this->_DEBUG_SQL($sql);

                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }


    public function getUserPoints($aStudent_ID)
    // Return Sum of users points
    {
	$points = "0";

        $sql = "SELECT sum(points) as points from tblsurveys_completed WHERE student_id = '" . $aStudent_ID . "'";
        $this->_DEBUG_SQL($sql);
		
		$result = $this->_db_conn->query($sql);

	if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {
                        return $row["points"];
                }
        } else {
                        return $points;
        }

    }




   public function getGlobalParam($param_name)
    // Return value from RHUL Survey parameters table
    {

        $sql = "SELECT value from tblsurvey_global WHERE param = '" . $param_name . "'";
		$this->_DEBUG_SQL($sql);
		
		$result = $this->_db_conn->query($sql);

        if ($result->num_rows > 0) {

        	while($row = $result->fetch_assoc())
		{
               		return $row["value"];
                }
        } else {
                        return "";
        }

    }


    public function updateGlobalParam($aParam, $aValue)
        // Update a parameter in the RHUL Survey parameters table
    {

                $sql = "UPDATE tblsurvey_global SET value = '" . $aValue . "' WHERE param = '" . $aParam . "'";
				$this->_DEBUG_SQL($sql);
				
                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }

    public function sendContactEmail($aFrom, $aMessage)
    // Send an email message from the user contact to the choose-survey team
    {
	$contact_dest_eml = $this->getGlobalParam("contact_email");
	if ($contact_dest_eml == "") { $contact_dest_eml = $this->EML_CHOOSE; }

	//$aMessage = $aMessage . "\n\nMessage sent from Choose-Survey system";

	return $this->sendEmail($contact_dest_eml, $aFrom, "Choose-Survey contact", $aMessage);
    }


    public function sendEmail($aTo, $aFrom, $aSubject, $aMessage)
    // Send email function, with wordwrap
    {

	// Wordwrap lines > 70
	$aMessage = wordwrap($aMessage,70);

	//echo "To: ", $aTo, " From: ", $aFrom, " Subject: ", $aSubject, " Message: ", $aMessage;
	$Headers = "From: " . $aFrom . "\r\n";

	// send email
	return mail($aTo, $aSubject ,$aMessage, $Headers);
    }






function getRHUL_LDAP_FieldValue($aStudent_ID, $aField) {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's given LDAP Field from currently logged in, LDAP authenticated RHUL user
 */

	$sql = "SELECT meta_value FROM cswp_usermeta WHERE meta_key = '" . $aField . "' AND user_id = "
             . "(SELECT user_id FROM cswp_usermeta WHERE meta_key = 'adi_studentno' AND meta_value = '" . $aStudent_ID . "');";

	$this->_DEBUG_SQL($sql);
	$result = $this->_db_conn->query($sql);
	
	if (!$result) return "";

	if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {
                        return $row["meta_value"];
                }
        } else {
                        return "";
        }



}


function getLotteryTicketCount($aStudent_ID)
{

	$sql = "SELECT sum(points) as total FROM tblsurveys_completed WHERE STR_TO_DATE(date, '%d/%m/%Y') > (SELECT STR_TO_DATE(value,'%d/%m/%Y') FROM tblsurvey_global WHERE param = 'prev_lottery_date' AND student_id = '" . $aStudent_ID . "')";

	$this->_DEBUG_SQL($sql);
		
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {
                        return $row["total"];
                }
        } else {
                        return "";
        }


}

function getVoucherTerms($aVoucher_ID)
{

        $sql = "SELECT terms, expiry_date from vwvouchers where voucher_code = '" . $aVoucher_ID . "'";
		$this->_DEBUG_SQL($sql);		
        
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {
                        return $row["terms"] . ". Claim before: " . $row["expiry_date"];
                }
        } else {
                        return "";
        }


}

function getVoucherList($student_id)
{
	// Find un-redeemed vouchers for given student
        $sql = "SELECT * FROM vwvouchers where student_id='" . $student_id . "' AND redeem_date is NULL AND STR_TO_DATE(expiry_date, '%d/%m/%Y') >= curdate();";
		$this->_DEBUG_SQL($sql);		
        
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                $Vouchers = "";

                while($row = $result->fetch_assoc())
                {
			$VoucherExpiryTerms = "<font size=2> " . $row["terms"] . ". Claim by " . $row["expiry_date"] . "</font>";
			$VoucherURL = $this->VOUCHER_WEB_VIEW . $student_id . "_" . $row["voucher_code"];
			$VoucherURL_HTML = "<a href='" . $VoucherURL . "'>" . $row["display_name"] . "</a>";
                        $Vouchers = $Vouchers . "Voucher (" . $row["issue_date"] . "): " . $VoucherURL_HTML . $VoucherExpiryTerms . " <br>";
                }

                return $Vouchers;

        } else {
                        return "";
        }

}

function getAutoRewards()
{

        $sql = "SELECT * FROM tblsurvey_auto_rewards where STR_TO_DATE(expiry_date, '%d/%m/%Y') >= curdate()";
		$this->_DEBUG_SQL($sql);

        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

		$Rewards = "";

                while($row = $result->fetch_assoc())
                {

			$freq = "";
			$type = "";
			$points = "";
			$at_one = "";
			if ($row["survey_occur"]) { $type = " surveys"; $points = $row["survey_occur"]; } else { $type = " points"; $points = $row["point_occur"]; };
			if ($row["type"] == "R") { $freq = " every "; } else { $freq = " at "; };

			if ($row["start_at_one"]) { $at_one = ", starting at your first!"; };

			$Rewards = $Rewards . $row["display_name"] . $freq . $points . $type . $at_one . "<br>";

                }
		
		return $Rewards;

        } else {
                        return "";
        }
}


function getAutoRewardsListAdmin()
{
        $sql = "SELECT * FROM tblsurvey_auto_rewards";
		$this->_DEBUG_SQL($sql);
		
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                $SchemeList = "<table width='600'>";

                $SchemeList = $SchemeList . "<tr><td><b>Name</b></td><td><b>DisplayName</b></td><td><b>Type</b></td><td><b>Occurrence</b></td><td><b>Start at 1</b></td><td><b>Expires</b></td></tr>";


                while($row = $result->fetch_assoc())
                {

		if ($row["survey_occur"]) { $occur = $row["survey_occur"] . " survey(s)"; } else { $occur = $row["point_occur"] . " point(s)"; }

                        $SchemeList = $SchemeList . "<tr><td>" . $row["title"] . "</td><td>" . $row["display_name"] . "</td><td>" . $row["type"] . "</td><td>" . $occur . "</td><td>" . $row["start_at_one"] . "</td><td>" . $row["expiry_date"] . "</td></tr>";

                }

                $SchemeList = $SchemeList . "</table>";

                return $SchemeList;

        } else {
                        return "";
        }
}


function postNewAutoRewardsScheme($title, $displayname, $type, $survey_occur, $point_occur, $start_at_one, $expiry, $terms)
      // Create a new auto rewards survey voucher scheme
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

		 if (trim($expiry) == "") { $expiry = "NULL"; } else { $expiry = "'" . $expiry . "'"; }
		 if (trim($point_occur) == "") { $point_occur = "NULL"; } else { $point_occur = "'" . $point_occur . "'"; }

                $sql = "INSERT INTO tblsurvey_auto_rewards (title, display_name, type, survey_occur, point_occur, start_at_one, expiry_date, terms) VALUES ('" . $title . "','" . $displayname . "','" . $type  . "','" . $survey_occur  . "'," . $point_occur  . ",'" . $start_at_one . "', " . $expiry . ",'" . $terms . "')";

				$this->_DEBUG_SQL($sql);

                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }


       
function getVoucherListAdmin()
{
		$sql = "SELECT * FROM tblsurvey_vouchers WHERE redeem_date IS NULL AND STR_TO_DATE(expiry_date, '%d/%m/%Y') >= curdate() ORDER BY issue_date DESC";

        $this->_DEBUG_SQL($sql);
		
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                $VoucherList = "<table width='550'>";

		$VoucherList = $VoucherList . "<tr><td><b>Fastcode</b></td><td><b>Student ID</b></td><td><b>Issue date</b></td><td><b>Voucher code</b></td></tr>";


                while($row = $result->fetch_assoc())
                {

                        $VoucherList = $VoucherList . "<tr><td>" . $row["id"] . "</td><td>" . $row["student_id"] . "</td><td>" . $row["issue_date"] . "</td><td>" . $row["voucher_code"] . "</td></tr>";

                }

		$VoucherList = $VoucherList . "</table>";

                return $VoucherList;

        } else {
                        return "";
        }
}


function getVoucherCodeFromFastCode($aFastCode) {
/**
 * Jamie Alnasir, created.
 * Obtain Voucher code given Fastcode
 */

        $sql = "SELECT voucher_code FROM tblsurvey_vouchers where id = " . $aFastCode;
		$this->_DEBUG_SQL($sql);
		
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {
                        return $row["voucher_code"];
                }
        } else {
                        return "";
        }



}

function getStudentIDFromVoucherCode($aVoucherCode) {
/**
 * Jamie Alnasir, created.
 * Obtain Student ID from Voucher code
 */

        $sql = "SELECT student_id FROM tblsurvey_vouchers where voucher_code = '" . $aVoucherCode . "'";
		$this->_DEBUG_SQL($sql);
		
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {
                        return $row["student_id"];
                }
        } else {
                        return "";
        }



}

public function getVoucherFileFromVoucherCode($aVoucherCode)
// Return voucher filename, which is a combination of student_id and voucherid
{
	return $this->getStudentIDFromVoucherCode($aVoucherCode) . "_" . $aVoucherCode . ".png";
}

public function purgeVoucher($aVoucher)
// Write voucher code to log for subsequent removal of QR png file
{
	if (is_numeric($aVoucher))
	{
		$aVoucher = $this->getVoucherCodeFromFastCode($aVoucher);
	}

	//print "QR flagged for purge: ". $aVoucher . " ";

	file_put_contents($this->VOUCHER_PURGE_FILE, $this->getVoucherFileFromVoucherCode($aVoucher) . "\n", FILE_APPEND | LOCK_EX);

}

public function redeemVoucher($aVoucher)
    // Redeem a voucher by it's voucher ID or fastcode 
    {

	$datetime = date("d/m/Y");

	// Ensure redeem_date for already redeemed vouchers is not
	// updated and overwritten should voucher_code/id be re-entered
	// multiple times ( add " AND redeem_date IS NULL" to WHERE clause )
	if (is_numeric($aVoucher))
	{		
        	$sql = "UPDATE tblsurvey_vouchers SET redeem_date = '" . $datetime . "' WHERE id = " . $aVoucher . " AND redeem_date IS NULL";
	} else
	{
		$sql = "UPDATE tblsurvey_vouchers SET redeem_date = '" . $datetime . "' WHERE voucher_code = '" . $aVoucher . "' AND redeem_date IS NULL";
	}

	$this->_DEBUG_SQL($sql);
        $result = $this->_db_conn->query($sql);


        if (mysqli_affected_rows($this->_db_conn) <> 0) {
	    $this->purgeVoucher($aVoucher);
            return 0;
        } else {
            return -1;
        }

    }



public function redeemVoucherList($aVoucherList)
        // Redeem vouchers by iterating voucher list
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

                $aVoucherList = mysqli_real_escape_string($this->_db_conn, $aVoucherList);
		$vouchers = array_filter(explode('\r\n', $aVoucherList));

		$purge = 0;

		foreach ($vouchers as $voucher)
		{
			if ($this->redeemVoucher(trim($voucher)) == 0)
			{
				$purge = $purge + 1;
				print $this->HTML_IMG_Tick . "voucher " . $voucher . " successfully redeemed and widthdrawn from circulation.<br>";
			} else
			{
				print $this->HTML_IMG_Warn . " voucher " . $voucher . " was NOT redeemed, it was either not found or has already been redeemed!<br>";
			}
		}

		if ($purge <> 0) { $this->doVoucherFilePurge(); }
    }




public function doVoucherFilePurge()
// Perform the voucher purge utilising the VOUCHER_PURGE and VOUCHER_MANIFEST
// Call external purge bash script
{
	print exec($this->VOUCHER_PURGE_SCRIPT);
	print "<br>" . $this->HTML_IMG_Tick . " Voucher QR code file purge completed by operating system.<br>";
}



function execChooseReportList()
{
        // Find un-redeemed vouchers for given student
        $sql = "SELECT * FROM tblsurvey_reports";
		$this->_DEBUG_SQL($sql);
		
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                echo "<table width=550><tr><td><b>Report Title</b></td></tr>";

                while($row = $result->fetch_assoc())
                {
                        $Report_URL = "<a href='/choose-admin/report-view.php?id=" . $row["id"] . "'>" . $row["title"] . "</a>";
                        echo "<tr><td>" . $Report_URL . "</td></tr>";
                }

                echo "</table>";

        } else {
                        return "";
        }

}


public function execChooseReport($report_id, $width, $sort_col)
{
 
        $sql = "SELECT * FROM tblsurvey_reports where id = " . $report_id;
	$this->_DEBUG_SQL($sql);
	
        $result = $this->_db_conn->query($sql);

        if (!$result) return "";

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc())
                {

			if ($this->REPORT_SQL) {
			echo $this->HTML_IMG_Tick . "<font size=2><b>Report SQL: (NB: vw in query implies hidden SQL in underlying SQL View)</b></font>";
			echo "<table width=550 border=1 cellspacing=0 bgcolor=#E6FAE6><tr>";
			echo "<td><font size=1>" . $row["sql"] . ";</font></td>";
			echo "</table><br>";
			}

			echo "<b><b>" . $row["title"] . "</b><br><br>";
			$cols = explode(",", $row["col_fields"]);
                        $this->MySQL_Table_to_HTML_SQL($row["sql"], $width, $cols, $sort_col);
                }
        } else {
                        return "";
        }

}

public function MySQL_Table_to_HTML($table_name, $width, $cols, $sort_col)
{
       if ($sort_col == "")
        {
                $sql = "SELECT * FROM " . $table_name;
        } else
        {
                $sql = "SELECT * FROM " . $table_name . " ORDER BY " . $sort_col;
        }

	$this->MySQL_Table_to_HTML_SQL($sql, $width, $cols, $sort_col);

}

public function MySQL_Table_to_HTML_SQL($sql, $width, $cols, $sort_col)
{

	// Ensure SQL "ORDER BY" hasn't previously been applied higher up
	if (($sort_col <> "") && (strpos(strtolower($sql), 'order by') == false))
        {
                $sql = $sql . " ORDER BY " . $sort_col;
        } else if ($sort_col <> "")
	{
	// Remove previous "ORDER BY" clause and construct new one
		$sql = substr($sql, 0, strpos(strtolower($sql), 'order by') ) . " ORDER BY " . $sort_col;
	}


	$result = $this->_db_conn->query($sql);
	$this->_DEBUG_SQL($sql);
	
	
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


    public function logGCM_Reg_ID($aIP, $aReg_ID)
        // Log GCM device registration ID with IP address
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

                $sql = "INSERT INTO tblgcm_reg (ip, reg_id) VALUES ('" . mysqli_real_escape_string($this->_db_conn, $aIP) . "', '" . mysqli_real_escape_string($this->_db_conn, $aReg_ID) . "')";

                $this->_DEBUG_SQL($sql);
                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }

    public function logGCM_Msg($aSubj, $aMsg)
        // Log GCM Push notification message
        // Must use mysqli_real_escape_string to correctly escape characters and
        // prevent SQL Injection attack, see PHP documentation below:
        // http://php.net/manual/en/security.database.sql-injection.php
    {

                $sql = "INSERT INTO tblgcm_msg_log (subj, msg) VALUES ('" . mysqli_real_escape_string($this->_db_conn, $aSubj) . "', '" . mysqli_real_escape_string($this->_db_conn, $aMsg) . "')";

                $this->_DEBUG_SQL($sql);
                if ($this->_db_conn->query($sql) === TRUE) {
                    return 0;
                } else {
                    return -1;
                }
    }


    public function getGCMList()
    // Obtain list of GCM registered devices
    {

                $sql = "SELECT reg_id, pretty_id, ip FROM tblgcm_reg WHERE reg_id NOT like 'IOS_TOKEN%';";
                $this->_DEBUG_SQL($sql);

                $result = $this->_db_conn->query($sql);

                if ($result->num_rows > 0) {
			$lstResult = array();
			while($row = $result->fetch_assoc()) {
				array_push($lstResult, $row);
			}
                        return $lstResult;
                } else {
                        return 0;
                }

    }

    public function getFirebaseList()
    // Obtain list of Firebase registered iOS devices
    {

                $sql = "SELECT SUBSTRING(reg_id, LENGTH('IOS_TOKEN:')+1) as reg_id, pretty_id, ip FROM tblgcm_reg where reg_id LIKE 'IOS_TOKEN:%';";
                $this->_DEBUG_SQL($sql);

                $result = $this->_db_conn->query($sql);

                if ($result->num_rows > 0) {
                        $lstResult = array();
                        while($row = $result->fetch_assoc()) {
                                array_push($lstResult, $row);
                        }
                        return $lstResult;
                } else {
                        return 0;
                }

    }


    public function getGCMDeviceListAdmin()
    // Obtain list of Google Cloud registered devices
    {

                $sql = "SELECT reg_id, pretty_id, ip, date FROM tblgcm_reg";
                $this->_DEBUG_SQL($sql);

                $result = $this->_db_conn->query($sql);

                if ($result->num_rows > 0) {

			echo "<table width='600'>";
			echo TR( TD(B("ID")) . TD(B("Date registered")) . TD(B("IP-Address")) . TD(B("Google Reg. ID")) );

                        while($row = $result->fetch_assoc()) {
				$reg_id = $row['reg_id'];
				$pretty_reg_id = substr($reg_id, 0, 20) . "...";
				echo TR( TD($row['pretty_id']) . TD($row['date']) . TD($row['ip']) . TD($pretty_reg_id) );
                        }
			echo "</table>";
                } else {
                        return 0;
                }

    }

    public function getGCMMessageListAdmin()
    // Obtain list of the messages sent via Google Cloud
    {

                $sql = "SELECT msg_id, date, subj, msg FROM tblgcm_msg_log order by msg_id desc;";
                $this->_DEBUG_SQL($sql);

                $result = $this->_db_conn->query($sql);

                if ($result->num_rows > 0) {

                        echo "<table width='600'>";
                        echo TR( TD(B("ID")) . TD(B("Date sent")) . TD(B("Subject")) . TD(B("Message")) );

                        while($row = $result->fetch_assoc()) {
                                echo TR( TD($row['msg_id']) . TD($row['date']) . TD($row['subj']) . TD($row['msg']) );
                        }
                        echo "</table>";
                } else {
                        return 0;
                }

    }




}

?>

