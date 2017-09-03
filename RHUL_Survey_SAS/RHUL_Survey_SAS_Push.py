#!/usr/bin/python
#//==============================================================================
#// RHUL Survey Project - SurveyMonkey API Synch Google Push Notification System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for Economics Department
#// Copyright (c) 2016 Jamie J. Alnasir, All Rights Reserved
#//==============================================================================
#// Version: Python edition
#//==============================================================================

import RHUL_Survey_SAS_MySQL;
import os;
import subprocess;
import sys;
import time;
import string;
import random;
import qrcode;
from gcm import *;

test_reg_id = 'YOUR_TEST_GCM_REGISTED_DEVICE_ID_HERE';


class RHULGCMPushEngine:

	dbSurvey = RHUL_Survey_SAS_MySQL.RHULSurveyDB();

	# Google Cloud Messaging account API key
	GCM_API_KEY = "YOUR_GCM_API_KEY_HERE";
	
	def __init__(self):
		self.gcm = GCM(self.GCM_API_KEY);

	def pushNotification(self, target_reg_id, subj, msg):
	# Push a notification to a device via Google Cloud API
		data = {'subj': subj, 'msg': msg};
		print data;
		self.gcm.plaintext_request(registration_id=target_reg_id, data=data);

	def pushAll(self, subj, msg):
	# Push a notification to ALL registered devices by querying mySQL db of reg. devices
		lstReg = self.dbSurvey.dbGetGCMList();
		for reg in lstReg:
			pretty_id, reg_id, ip, date = reg;
			print "pushing notification to:";
			print pretty_id, reg_id, ip, date;
			self.pushNotification(reg_id, subj, msg);


PushEngine = RHULGCMPushEngine();

# Test notification
#PushEngine.pushNotification(test_reg_id, "subj", "msg");

if (len(sys.argv) <> 3):
	print;
	print "usage:";
	print "push.sh $@ \"subject\" \"message text\" ";
else:
	PushEngine.pushAll(sys.argv[1], sys.argv[2]);

