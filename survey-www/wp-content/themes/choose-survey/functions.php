<?php

$RHUL_Non_Stu_ID = "00000000";	// Needed to assign Survey monkey responses to non-student ID
				// if not student account

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
show_admin_bar(false);
}



function getRHUL_UserID() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's Student ID from currently logged in, LDAP authenticated RHUL user
 */

	global $RHUL_Non_Stu_ID;


	if ( is_user_logged_in() ) {

		global $wpdb;
		$wp_user = strtoupper(wp_get_current_user()->user_login); // Get currently logged-in Wordpress user

		$sql = "SELECT meta_value FROM cswp_usermeta WHERE meta_key = 'adi_studentno' AND user_id = "
			. "(SELECT user_id FROM cswp_usermeta WHERE meta_key = 'adi_samaccountname' AND meta_value = '" . $wp_user . "');";

		$results = $wpdb->get_results($sql);


		// In case the "adi_studentno" key does not exist (i.e. a staff member)
		// returns -1 for this!

		if (!$results) { return $RHUL_Non_Stu_ID; };

		try {
			return $results[0]->meta_value;

		} catch (Exception $e)
		{
			return -1;
		}





	} else {
		return -1;
	}
}

function getRHUL_LDAP_FieldValue($aField) {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's given LDAP Field from currently logged in, LDAP authenticated RHUL user
 */


        if ( is_user_logged_in() ) {

                global $wpdb;
                $wp_user = strtoupper(wp_get_current_user()->user_login); // Get currently logged-in Wordpress user

                $sql = "SELECT meta_value FROM cswp_usermeta WHERE meta_key = '" . $aField . "' AND user_id = "
                        . "(SELECT user_id FROM cswp_usermeta WHERE meta_key = 'adi_samaccountname' AND meta_value = '" . $wp_user . "');";

                $results = $wpdb->get_results($sql);

		return $results[0]->meta_value;

        } else {
                return ""; // Return empty string if field/value not found
        }
}


function getRHUL_DisplayName() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's Display name from currently logged in, LDAP authenticated RHUL user
 */
	return getRHUL_LDAP_FieldValue("adi_givenname");
}

function getRHUL_FirstName() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's first name from currently logged in, LDAP authenticated RHUL user
 */
        return getRHUL_LDAP_FieldValue("first_name");
}

function getRHUL_LastName() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's last name from currently logged in, LDAP authenticated RHUL user
 */
        return getRHUL_LDAP_FieldValue("last_name");
}

function getRHUL_FullName() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's full name from currently logged in, LDAP authenticated RHUL user
 */
        return getRHUL_FirstName() . " " . getRHUL_LastName();
}

function getRHUL_Email() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's Display name from currently logged in, LDAP authenticated RHUL user
 */
	return getRHUL_LDAP_FieldValue("adi_mail");
}

function getRHUL_StartYear() {
/**
 * Jamie Alnasir, created.
 * Obtain RHUL User's Start Year from currently logged in, LDAP authenticated RHUL user
 */
        $dname = getRHUL_LDAP_FieldValue("adi_displayname");
	if (strpos($dname, "("))
	{		
		$p = (int)strpos($dname, "(");
		return substr($dname, $p+1, 4);
	} else
	{
		return "0";
	}
}


function isRHULStudentAccount() {
	global $RHUL_Non_Stu_ID;
	if (getRHUL_UserID() == $RHUL_Non_Stu_ID) return -1;
}


function isRHULApp() {
/**
 * Jamie Alnasir, created.
 * Determine whether site is running from within a frame of an Android or iOS app
 * These apps load the Choose-Survey Wordpress site setting the RHUL_App parameter to "true"
 * i.e. https://choose-survey.royalholloway.ac.uk/?RHUL_App=true, therefore it is sufficient
 * just to check whether RHUL_App has been set, as direct access through a browser (even on a
 * mobile) will not require this URL parameter to be set.
 */

	if (isset($_GET['RHUL_App'])) {
		return true;
	} else {
		return false;
	}

}


function isMsgViewMode() {
/**
 * Jamie Alnasir, created.
 * Determine whether site is running in RHUL Survey Message view mode
 */

	if (isset($_GET['msg_id'])) {
		return true;
	} else {
		return false;
	}

}


function isContactMode() {
/**
 * Jamie Alnasir, created.
 * Determine whether site is running in RHUL Contact mode
 */

        if (isset($_GET['cont'])) {
                return true;
        } else {
                return false;
        }

}

function isEmailMode() {
/**
 * Jamie Alnasir, created.
 * Determine whether site is running in RHUL Email mode
 */

        if (isset($_POST['msg_text'])) {
                return true;
        } else {
                return false;
        }

}



?>
