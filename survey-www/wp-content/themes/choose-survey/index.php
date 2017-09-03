<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

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

include_once "const.inc.php";
include_once "db_conn.php";
include_once "surveyappuser.class.php";
include_once "cipher.php";

// Acquire DB connection ($db_conn) from db_conn.php
$SurveyApp = new SurveyAppUser($db_conn);

$RHUL_Stu_ID = getRHUL_UserID();
$RHUL_Stu_ID_Cipher = RHUL_Cipher( $RHUL_Stu_ID );
$RHUL_Stu_FullName = getRHUL_FullName();
$RHUL_Stu_DispName = getRHUL_DisplayName();
$RHUL_Stu_Email    = getRHUL_Email();
$RHUL_Stu_Start_Yr = getRHUL_StartYear();
$RHUL_TicketCount  = $SurveyApp->getLotteryTicketCount( $RHUL_Stu_ID );
$RHUL_Lottery_Date = $SurveyApp->getGlobalParam("lottery_date");
$RHUL_Auto_Rewards = $SurveyApp->getAutoRewards();
$RHUL_Stu_Consented = $SurveyApp->didConsent($RHUL_Stu_ID);
$Prize1 = $SurveyApp->getGlobalParam("prize1");
$Prize2 = $SurveyApp->getGlobalParam("prize2");
$RHUL_Stu_Email_Qualified = $RHUL_Stu_FullName . "<" . $RHUL_Stu_Email . ">";
$WP_Login_URL = "wp-login.php";
$WP_Logout_URL = "wp-login.php?action=logout";
$HTML_Home = "<b><a class='btn' href='/'>Home</a></b>";
$HTML_Logout = "<b><a href='$WP_Logout_URL'>log out</a></b>";
$HTML_Login = "<b><a class='btn' href='$WP_Login_URL'>log in</a></b>";
$HTML_Send  = "<b><a class='btn' onclick='frmContact.submit();'>Send</a></b>";
$HTML_Contact = "<a href='/index.php?cont=1'>Contact us</a>";
$HTML_Back    = "<a class='btn' href='/'>back</a>";
$HTML_Warn    = "<img src='/img/warning.png' title='Warning!'>";
$HTML_Notice   = "<img src='/img/notice.png' title='Notice!'>";
$HTML_Arrow   = "<img src='/img/arrow.png' title='Look!'>";

if (isRHULStudentAccount()) { echo $HTML_Warn . " <font color='red'>This is not a valid student account, the app may not function as expected!</font>"; };


$RHUL_User_Points = $SurveyApp->getUserPoints( $RHUL_Stu_ID );
if ($RHUL_User_Points == "") { $RHUL_User_Points = "0"; }

$DEBUG_MODE = 0;
$CIPHER = 0;

//echo "test: " . isset($_POST['msg_text']);

// If we are in MsgViewMode, obtain the RHUL Survey message subject and content...
if (isMsgViewMode()) {

	$Survey_Msg = $SurveyApp->getSurveyMessage( $_GET['msg_id'] );
}
?>


	

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">


<!-- Login/Login details pane -->
<?php if($RHUL_Stu_ID != -1) : ?>
	<table border="0" width="100%" bgcolor=#EEEEEE>
	  <tr>
		<td width="33%" align="center"><b><?php echo "Hello " . $RHUL_Stu_DispName; ?></b></td>
		<td width="33%" align="center"><center><b><a href="https://choose-survey.royalholloway.ac.uk/">Home</a></b></center></td>
		<td width="33%" align="center"><center><b><?php echo $HTML_Logout; ?></b></center></td>
	  </tr>
	</table>
<?php else : ?>
 <table border="0" width="100%" bgcolor=#EEEEEE>
          <tr>
                <td width="100%" align="center"><center>Welcome to <b>Choose-Survey,</b> please <b><a href="<?php echo $WP_Login_URL; ?>">log in</a></b> to get started...</center></td>
          </tr>
        </table>
<?php endif; 

  if($RHUL_Stu_ID != -1)
  {
      if (!$RHUL_Stu_Consented) { echo $HTML_Notice . " <font color='green'>Please complete the consent survey!</font><br><br>"; 
  }

};

?>


<!-- Message pane -->
<?php if($RHUL_Stu_ID != -1) : ?>

       <table border="1" width="98%" bgcolor="#DBEbF6">
       <td width="100%">

<?php
	// RHUL App Messages bar

		if (isMsgViewMode()) {
			// Print the Survey Message Subject
			echo "<b>" . $Survey_Msg[0] . "</b>";
		} elseif (isContactMode())
		{
			echo "<b>Contact us</b> - enter your message below:";
		} else
		{
			if ($RHUL_Stu_ID != -1)
			{
				// Print the Survey Message list				
				echo "<b>News</b><br>";
				$SurveyApp->getSurveyMessageList();

			}
		}
	?>
       </td>
       </tr>       
       </table>
    
<?php endif; ?>

       <table border="1" style='border-style: none;' width="98%" bgcolor=DAEDDA>
       <td width="100%">
	<?php
	// RHUL App Main content area
	if (isMsgViewMode()) {

		// Print the Survey Message Body (i.e. it's content)

		echo $Survey_Msg[1];
		echo "<br><br>" . $HTML_Home;
	} elseif (isEmailMode()) {

		// Send Email mode

		echo "<b>From: " . $RHUL_Stu_DispName . "  &lt;" . strtolower($RHUL_Stu_Email) . "&gt;</b><br>";
		$ContactMsg = htmlspecialchars($_POST["msg_text"]);
		$User_Vars = "Student ID: " . $RHUL_Stu_ID . " User IP-address: " . $_SERVER["REMOTE_ADDR"] . "\nUser Browser: " . $_SERVER["HTTP_USER_AGENT"] . "\n\n";

		echo "<br>";

		if ($SurveyApp->sendContactEmail($RHUL_Stu_Email_Qualified, $User_Vars . $ContactMsg))
		{
               		echo "<b>Thank you for your message, we will get back to you shortly.</b><br>";
	                echo "(A copy of your sent message is below):<br><br>";
                        echo nl2br($ContactMsg) . "<br>";

		} else
		{
			echo "<b>Unfortunately an error occurred and your email message was not sent!</b><br>";
			echo "Please email your message directly to " . $SurveyApp->getGlobalParam("contact_email") . "<br>";

		}

		echo "<br>";
                echo $HTML_Back;

	} elseif (isContactMode()) {

		// Display form for user to enter contact message

		echo "<form name='frmContact' action='/index.php' method='post'>";
		echo "<b>From: " . $RHUL_Stu_DispName . "  &lt;" . strtolower($RHUL_Stu_Email) . "&gt;</b><br>";
		echo "<textarea rows='10' type='hidden' id='msg_text' name='msg_text'></textarea>";
		echo $HTML_Send . "  " . $HTML_Back;
		echo "</form>";
	} else
	{
		if ($RHUL_Stu_ID != -1)
		{
			echo "<table border='0' style='border-style: none;'>";
			echo "<tr><td width='47%' valign='top' style='border-style: none;'>";


			echo "<b>Your new surveys</b><br><br>\n";
                        $SurveyApp->getUncompletedSurveyList($RHUL_Stu_ID, $RHUL_Stu_Start_Yr);


			echo "<td width='6%' style='border-style: none'><br><br> <!-- center arrow --> </td>";

			echo "<td width='47%' valign='top' style='border-style: none;'><b>Your recently completed surveys</b><br><br>";

			echo "<font color='grey'>";
                        if ($CIPHER)
                        {
                                $SurveyApp->getCompletedSurveyList( RHUL_Cipher( $RHUL_Stu_ID ) );
                        } else
                        {
                                $SurveyApp->getCompletedSurveyList( $RHUL_Stu_ID );
                        }
			echo "</font>";


			echo "</td></tr>";
			echo "</table>";


		} else
		{
			echo "<center><b>Please log in using your student account details<b><br><br></center>\n";
			echo "<center><b>$HTML_Login</a><b><br><br></center>\n";
		}
	}
?>
	</td></tr>
	</table>


<!-- Points pane -->
<?php if( ($RHUL_Stu_ID != -1) && !(isMsgViewMode()) && (!isContactMode()) ): ?>
<table border="0" width="100%" bgcolor=#DAEDDA>
<tr>
      <td width="33%" align="center"><b>Thanks for your participation!<br><br><?php echo $HTML_Arrow . "You have gained " . $RHUL_User_Points . " CHOOSE points so far!"; ?><br><br>
You can win prizes in our CHOOSE lottery on [<?php echo $RHUL_Lottery_Date; ?>]<br>
You have gained <?php echo $RHUL_TicketCount; ?> lottery tickets, one for each of your CHOOSE points.<br>
<br>
The lottery prizes are:</b><br>
<?php echo $Prize1; ?><br>
<?php echo $Prize2; ?><br>
<img width='200' height='137' style='width:200px;height:137px;' src='/img/prize1.jpg' title='<?php echo $Prize1; ?>'>
<img width='200' height='137' style='width:200px;height:137px;' src='/img/prize2.jpg' title='<?php echo $Prize2; ?>'>
<br><br>
Don't forget to claim your...<br>
<?php echo $RHUL_Auto_Rewards; ?>
<br>
<b>Your Free vouchers!</b><br>
<?php echo $SurveyApp->getVoucherList($RHUL_Stu_ID); ?>

</td>
</tr>
</table>
<?php endif; ?>

<!-- Contact pane -->
<?php if ( !isMsgViewMode() && !isContactMode() ): ?>
<table border="0" width="100%" bgcolor=#EEEEEE>
<tr>
<td>Having trouble or questions? <?php echo $HTML_Contact; ?></td>
</table>
</table>
<?php endif; ?>



		</main><!-- .site-main -->
	</div><!-- .content-area -->


<?php get_footer(); ?>


