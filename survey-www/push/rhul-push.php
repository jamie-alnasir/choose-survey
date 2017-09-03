<?php
#//==============================================================================
#// RHUL Survey Project - SurveyMonkey API Synch Google Push Notification System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for Economics Department
#// Copyright (c) 2016 Jamie J. Alnasir, All Rights Reserved
#//==============================================================================
#// Version: PHP edition
#//==============================================================================

include_once "../wp-content/themes/choose-survey/const.inc.php";
include_once "../wp-content/themes/choose-survey/db_conn.php";
include_once "../wp-content/themes/choose-survey/surveyappuser.class.php";
include_once "./const.inc.php";

# Added support for Firebase. Firebase device tokens registered with RHUL server
# are prepended with "IOS_TOKEN:" to allow differentiation between the two CURL
# destination URLs. Firebase messages are pushed out to a news group with which
# all devices are registered ("/topics/news").


	class RHULGCMPushEngine
	{

		# RHUL SurveyApp Engine
		private $SurveyApp;
		private $PUSH_BATCH_SIZE = 100;
		private $LOG_PUSH_EVENTS = "/home/mxba001/survey-www/push.123/push_event.log";
		public  $DEBUG_MODE = 0; // Set to 1 to Enable, 0 to Disable
		public $HTML_IMG_Warn = "<img src='/img/warning.png' title='Warning!'>";

		# Google Cloud Messaging account API key
		public $GCM_API_KEY = "AIzaSyDXVCQ23jw5fMYLvYVhCLC4GJpclv_7qg8";
		public $GCM_URL = "https://android.googleapis.com/gcm/send";
		# Firebase Messaging account API Key
		public $FIREBASE_API_KEY = "AAAAW0g4cYQ:APA91bEUKLe7FwvbO0AZsCbT_8gmVFOtP7oD4TYuLMY4tRjDej1MS3VHkPrOZTAobAFgyXYmwaAuswVFeLwX9FzFyoT1IgmRXbT4SF7RYJhjUadUNx1Ub40CLXb6EaxvKn_H3DvQxDaddbebQoEtsAslrTlAnqm-Mg";
		public $FIREBASE_URL = "https://fcm.googleapis.com/fcm/send";

		// Developer test registration ID
		public $TEST_REG_ID =  "APA91bEnaniiuaE2RrtNNno4vmCdfLfRJouyqkIy6PX-XjBPyymS_QkUjHAGisBYg-rIZfx-IBPMTsWySnrdQTln_ZCX-QAb1X1jp233VR56t2khyaYzyr8";


		public function __construct($db_conn)
		// Constructor
		{
			$this->SurveyApp = new SurveyAppUser($db_conn);
			if ($this->DEBUG_MODE)
                	{
                        	echo $this->HTML_IMG_Warn . "<font size=2><b> Debug mode is enabled. This is not an error, the system is being maintained/tested. You may see verbose diagnostic messages. You may see bugs, they are not real! Dont't panic! Work as usual.</b></font><hr size=1>";
                	}
		}

		public function logEvent($event, $msg)
		{
			$now = date("d-m-Y H:i:s");
			$entry = $now . " " . $event . ": " . $msg . "\n";
			file_put_contents($this->LOG_PUSH_EVENTS, $entry, FILE_APPEND);
		}

		public function logNewline()
		{
			file_put_contents($this->LOG_PUSH_EVENTS, "----", FILE_APPEND);
		}

		public function logGCM_Msg($aSubj, $aMsg)
		{
			$this->SurveyApp->logGCM_Msg($aSubj, $aMsg);
		}

		public function pushNotificationBatch($arr_target_reg_id, $subj, $msg)
                # Jamie Alnasir implemented
                # Push a batch of push notifications to devices in array $arr_target_reg_id
                {
                        $headers = array(
                        'Authorization: key=' . $this->GCM_API_KEY,
                        'Content-Type: application/json'
                        );

                        $fields = array(
                        'registration_ids' => $arr_target_reg_id,
                        'data' => array( "subj" =>$subj, "msg" => $msg )
                        );

                        // Open connection
                        $ch = curl_init();

                        // Set the URL, number of POST vars, POST data
                        curl_setopt( $ch, CURLOPT_URL, $this->GCM_URL);
                        curl_setopt( $ch, CURLOPT_POST, true);
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

                        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

                        // Execute post
                        $result = curl_exec($ch);

                        // Close connection
                        curl_close($ch);
                }

		public function pushFB($subj, $msg)
		{
	
	        	$fields = array(
                               'notification' => array( "title" =>$subj, "body" => $msg ),
			       'to' => '/topics/news');
			var_dump(json_encode($fields));

                        $headers = array(
                        'Authorization: key=' . $this->FIREBASE_API_KEY,
                        'Content-Type: application/json'
                        );

			
			// Open connection
                        $ch = curl_init();

                        // Set the URL, number of POST vars, POST data
                        curl_setopt( $ch, CURLOPT_URL, $this->FIREBASE_URL);
                        curl_setopt( $ch, CURLOPT_POST, true);
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

                        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

                        // Execute post
                        $result = curl_exec($ch);

                        // Close connection
                        curl_close($ch);	
		}

		public function pushBatch($lst, $subj, $msg)
		{
			echo "Pushing batch of " . sizeof($lst) . " notification(s)<br>";
			#var_dump($lst);
	
			if (!$this->DEBUG_MODE)
			{		
				# Despatch the notification to a batch of devices
				$this->pushNotificationBatch($lst, $subj, $msg, $firebase);
			}
			
			$this->logEvent("Pushed batch", sizeof($lst) . " item(s)");
		}

		public function pushSingle($gcm_id, $subj, $msg)
		{
			echo "Pushing single notification to device: " . $gcm_id;
			$lst = array($gcm_id);
			$this->pushNotificationBatch($lst, $subj, $msg);
			$tihs->logEvent("Pushed single notification to device: " . $gcm_id);
		}
		
		public function pushAll($subj, $msg)
		# Jamie Alnasir implemented
		# Push notifications to ALL devices in batches of $PUSH_BATCH_SIZE
		{
			# Push to GCM registered devices
			$lstGCM = $this->SurveyApp->getGCMList();
			$GCMLen = sizeof($lstGCM);
			$c = 0;
			$this->logGCM_Msg($subj, $msg);
			echo $GCMLen . " target GCM registered device(s) found in DB<br>";
			$this->logEvent("Push All", $GCMLen . " target devices(s) in DB");
			$this->logEvent("Payload [" . $subj, $msg . "]");

				$lstBatch = array();
				foreach ($lstGCM as $reg)
				{
					array_push($lstBatch, $reg["reg_id"]);
					$c++;
					if ($c == $this->PUSH_BATCH_SIZE)
					{
						$this->pushBatch($lstBatch, $subj, $msg);
						$lstBatch = array();
						$c = 0;
					}
				}
				$this->pushBatch($lstBatch, $subj, $msg);
			$this->logNewline();


			# Push to Firebase registered devices
			$lstFB = $this->SurveyApp->getFirebaseList();
			$FBLen = sizeof($lstFB);
                        $c = 0;
                        $this->logGCM_Msg($subj, $msg);
                        echo $FBLen . " target Firebase iOS device(s) found in DB<br>";
                        $this->logEvent("Push All", $FBLen . " target devices(s) in DB");
                        $this->logEvent("Payload [" . $subj, $msg . "]");
			$this->pushFB($subj, $msg);

		}
	}

#$GCM = new RHULGCMPushEngine($db_conn);

# Test code
#$tmp = array($GCM->TEST_REG_ID);
//var_dump($tmp);
#$GCM->logGCM_Msg("subject12345678910111213888888AA1234567890", "This is a test survey push notification message");
#$GCM->pushNotificationBatch($tmp, "subj", "msg");
#$GCM->pushAll("subj.all", "msg.all");

?>

