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


	class RHULGCMPushEngine
	{

		# RHUL SurveyApp Engine
		private $SurveyApp;
		private $PUSH_BATCH_SIZE = 100;
		private $LOG_PUSH_EVENTS = "./push_event.log";

		# Google Cloud Messaging account API key
		public $GCM_API_KEY = "AIzaSyDXVCQ23jw5fMYLvYVhCLC4GJpclv_7qg8";
		public $GCM_URL = "https://android.googleapis.com/gcm/send";

		// Developer test registration ID
		public $TEST_REG_ID =  "APA91bEnaniiuaE2RrtNNno4vmCdfLfRJouyqkIy6PX-XjBPyymS_QkUjHAGisBYg-rIZfx-IBPMTsWySnrdQTln_ZCX-QAb1X1jp233VR56t2khyaYzyr8";


		public function __construct($db_conn)
		// Constructor
		{
			$this->SurveyApp = new SurveyAppUser($db_conn);
		}

		public function logEvent($event, $msg)
		{
			$now = date("d-m-Y H:i:s");
			$entry = $now . " " . $event . ": " . $msg . "\n";
			file_put_contents($this->LOG_PUSH_EVENTS, $entry, FILE_APPEND);
		}

		public function logGCM_Msg($aSubj, $aMsg)
		{
			echo $this->SurveyApp->logGCM_Msg($aSubj, $aMsg);
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
			echo $result;


		}

		public function testBatch($lst)
		{
			echo sizeof($lst) . "<br>";
			var_dump($lst);
			echo "<br>";
			$this->logEvent("Pushed batch", sizeof($lst) . " item(s)");
		}
		
		public function pushAll($subj, $msg)
		# Jamie Alnasir implemented
		# Push notifications to ALL devices in batches of $PUSH_BATCH_SIZE
		{
				$lstGCM = $this->SurveyApp->getGCMList();
				#var_dump($lstGCM);
				$c = 0;
				$lstBatch = array();
				foreach ($lstGCM as $reg)
				{
					array_push($lstBatch, $reg["reg_id"]);
					$c++;
					if ($c == $this->PUSH_BATCH_SIZE)
					{
						$this->testBatch($lstBatch);
						$lstBatch = array();
						$c = 0;
					}
				}
				$this->testBatch($lstBatch);
		}

	}

$GCM = new RHULGCMPushEngine($db_conn);
$tmp = array($GCM->TEST_REG_ID);
//var_dump($tmp);

$GCM->logGCM_Msg("subject12345678910111213888888AA1234567890", "This is a test survey push notification message");
#$GCM->pushNotificationBatch($tmp, "subj", "msg");
$GCM->pushAll("subj.all", "msg.all");

?>

