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

// * Cipher the RHUL student ID to mitigate any attempt to fiddle with URLs
// * by substituting one student ID with another
 

function RHUL_Cipher($Unciphered)
/**
 * Jamie Alnasir, created.
 * Basic substitution Cipher with a twist
 */
{	
	$n_plain   = "0123456789";
	$n_cipher1 = "YOUR-10-DIGIT-CIPHER1-HERE";
	$n_cipher2 = "YOUR-10-DIGIT-CIPHER2-HERE";
	$result   = "";
		
	$strlen = strlen( $Unciphered );
	for( $i = 0; $i < $strlen; $i++ ) {
    
	$char = substr( $Unciphered, $i, 1 );
		if ($i % 2 == 0) {
			$result = $result . substr($n_cipher1, intval($char), 1);
		} else {
			$result = $result . substr($n_cipher2, intval($char), 1);
		}
	}	
	
	if ($strlen > 4)
	{
		$result = substr($result, 4, $strlen)
				. $result = substr($result, 0, 4);
	}

	return $result;
}


function RHUL_Decipher($Ciphered)
/**
 * Jamie Alnasir, created.
 * Basic substitution Decipher with a twist
 */
{
	$n_plain   = "0123456789";
	$n_cipher1 = "YOUR-10-DIGIT-CIPHER1-HERE";
	$n_cipher2 = "YOUR-10-DIGIT-CIPHER2-HERE";
	$result   = "";

	$strlen = strlen( $Ciphered );
	
	if ($strlen > 4)
	{
		$Ciphered = substr($Ciphered, $strlen - 4, $strlen)
				. $Ciphered = substr($Ciphered, 0, $strlen - 4);
	}

	for( $i = 0; $i < $strlen; $i++ ) {
    
		$char = substr( $Ciphered, $i, 1 );

		if ($i % 2 == 0) {
			$result = $result . substr($n_plain, strpos($n_cipher1, $char), 1);
		} else {
			$result = $result . substr($n_plain, strpos($n_cipher2, $char), 1);
		}	
	}
	
	return $result;
}

function getRHUL_UserID_Cipher($User_ID) {
/**
 * Jamie Alnasir, created.
 * Cipher the RHUL students ID to mitigate any attempt to fiddle with URLs
 * by substituting one student ID with another
 */
	return RHUL_Cipher( $User_ID );
}

function getRHUL_UserID_Decipher($User_ID_Cipher) {
/**
 * Jamie Alnasir, created.
 * Decipher the RHUL students ID (see above)
 */
	return RHUL_Decipher( $User_ID_Cipher );
}

?>
