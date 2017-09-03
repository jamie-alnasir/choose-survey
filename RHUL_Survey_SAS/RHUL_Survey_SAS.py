#!/usr/bin/python
#//==============================================================================
#// RHUL Survey Project - SurveyMonkey API Synchronisation System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for Economics Department
#// Copyright (c) 2014 Jamie J. Al-Nasir, All Rights Reserved
#//==============================================================================
#// Version: Python edition
#//==============================================================================

import RHUL_Survey_SAS_API;
import RHUL_Survey_SAS_MySQL;
import os;
import subprocess;
import sys;
import time;
import string;
import random;
import qrcode;

class RHULSurveyEngine:

	dbSurvey = RHUL_Survey_SAS_MySQL.RHULSurveyDB();
	SurveyAPI = RHUL_Survey_SAS_API.RHULSurveyMonkeyAPI();
	LAST_SYNC_DATE = "2013-01-01 00:00:00";
	POINTS_PER_SURVEY = "";
	VOUCHER_ID_LEN = 20;
	VOUCHERS_OUT_FOLDER = "/srv/www/survey/choose-vouchers/";
	VOUCHERS_MANIFEST_FILE   = "/home/mxba001/RHUL_Survey_SAS/VOUCHERS_MANIFEST.txt";
	VOUCHERS_PURGE_SCRIPT = "/home/mxba001/RHUL_Survey_SAS/purge-stale-vouchers.sh";
	
	def __init__(self):
		self.LAST_SYNC_DATE = self.dbSurvey.dbGetParam('last_sync_date');
		self.SurveyAPI.setLastSyncDate(self.LAST_SYNC_DATE);

		print "LAST_SYNC_DATE: " + self.LAST_SYNC_DATE;
		print self.SurveyAPI.LAST_SYNC_DATE;

		self.POINTS_PER_SURVEY = self.dbSurvey.dbGetParam('pts_per_survey');
		self.SurveyAPI.setPointsPerSurvey(self.POINTS_PER_SURVEY);
		self.dbSurvey.setPointsPerSurvey(self.POINTS_PER_SURVEY);
		print "POINTS PER SURVEY: " + self.SurveyAPI.POINTS_PER_SURVEY;

		0;

	def Debug(self):
		print 'ENTERING SAS DEBUG METHOD:'
		#self.dbSurvey.dbRewardVoucher(99, '100756725', 3, 'survey', '01-01-1980', 'voucher_123abc');
		#self.dbSurvey.dbSetParam('last_sync_date', '2008-01-01 00:00:00');
		#self.ComputeRewards('100756725');
		#self.ComputeAllRewards();

	def DeleteFile(self, afile):
		# First check if exists
		if os.path.isfile(afile):
		        os.remove(afile);
		else:
		        print("Error removing file: %s file not found" % afile);

	def RunProgram(self, afile):
		# First check if exists
		if os.path.isfile(afile):
			# TEMP -- should capture output/check exit code
			os.system(afile);
			#print subprocess.check_output([afile]);
		else:
			print("Error running program: %s file not found" % afile);


	def VoucherManifest(self):
		lstVouchers = self.dbSurvey.dbGetVoucherList();

		print "Generating live voucher manifest...";

		lstManifest = [];
		## NB: use append to add any non-voucher files that are to be kept
		## in the VOUCHERS_OUT_FOLDER to prevent them from being deleted;
		lstManifest.append("index.html");


		for voucher in lstVouchers:
			voucher_qr_file = str(voucher[2]) + '_' + voucher[5] + '.png';
			voucher_qr_filepath = self.VOUCHERS_OUT_FOLDER + voucher_qr_file;
			#print voucher;
			#print voucher_qr_file;
			if voucher[7] is not None:
				print "voucher " + voucher[5] + " successfully redeemed!";
			else:
				lstManifest.append(voucher_qr_file);
		
		# Write the manifest
		lstManifest.append(""); # for final \n
		fMan = open(self.VOUCHERS_MANIFEST_FILE, 'w');
		fMan.write("\n".join(lstManifest));

		# Use external program/script to purge stale vouchers (those not in the Manifest)
		print "Purging/removing stale vouchers...";
		#self.RunProgram(self.VOUCHERS_PURGE_SCRIPT);
				
		

	def GenerateVoucher(self, scheme_type, scheme_name, student_id, awarded_at, awarded_by, expiry_date):
		s=string.lowercase+string.digits;
		voucher_id=''.join(random.sample(s, self.VOUCHER_ID_LEN));
		voucher_id = 'voucher_' + voucher_id;
		issue_date = time.strftime("%d/%m/%Y %H:%M:%S");
		print voucher_id;
		vgenstatus = self.dbSurvey.dbRewardVoucher(scheme_type, student_id, awarded_at, awarded_by, issue_date, voucher_id, expiry_date);
		
		print "GENSTATE:" + str(vgenstatus);

		if (vgenstatus <> -1):
			voucher_fast_code = vgenstatus; # NB: Voucher gen return auto-inc rowid
							# otherwise use lookup: self.dbSurvey.dbGetVoucherFastCode(voucher_id);
			qr = qrcode.QRCode(
    				version=1,
   	 			error_correction=qrcode.constants.ERROR_CORRECT_L,
    				box_size=10,
   	 			border=4,
				);
			qr.add_data( 'voucher_id: ' + voucher_id );
			qr.add_data( ', student_id: ' + student_id );
			qr.add_data( ', scheme: ' + scheme_name );
			qr.add_data( ', issued: ' + issue_date );
			qr.add_data( ', fast_look: ' + str(voucher_fast_code) );
			qr.add_data( ', expiry_date: ' + expiry_date );
			qr.make(fit=True);
			img = qr.make_image();
			img.save(self.VOUCHERS_OUT_FOLDER + student_id + '_' + voucher_id + '.png');
			print "voucher generated: " + voucher_id + " fast-lookup: " + str(voucher_fast_code);
		else:
			print "voucher " + voucher_id + " not generated -- probably already exists!";

	def ComputeAllRewards(self):
		lstStudents = self.dbSurvey.dbGetStudentList();
		print lstStudents;
		for student in lstStudents:
			if (student[0] == ""):
				continue;
			self.ComputeRewards(student[0]);

	def ComputeRewards(self, student_id):
		lstAutoRewards = self.dbSurvey.dbGetAutoRewardList();
                lstCompSurveys = self.dbSurvey.dbGetCompletedSurveyList(student_id);

                #print lstCompSurveys;

                for autoreward in lstAutoRewards:
                        # print autoreward;

			# this auto-reward is awarded on a points basis
                        if not autoreward[4] is None:
                                print "by points";

			# This auto-reward is awarded on a survey-count basis
                        if not autoreward[5] is None:
			
				if (autoreward[3] == "R"):
				# Recurring reward scheme by survey count
					start_at = int(autoreward[5]) - 1;
					if (autoreward[6]) is not None:
						start_at = 0;

					#print "START-AT: " + str(start_at);
					#print "COMP-SURVEYS: " + str(len(lstCompSurveys));

					# Use lambda function to make range inclusive of last item
					# voucher_range = lambda start, end, increment: range(start, end+1, increment);

					awarded_at = start_at;
                        	        # for survey in voucher_range(start_at, len(lstCompSurveys), int(autoreward[5])):
					for survey in lstCompSurveys[start_at::int(autoreward[5])]:
						# GENERATE VOUCHER
						self.GenerateVoucher(int(autoreward[0]), autoreward[1] ,student_id, awarded_at + 1, 'survey', autoreward[7]);
						awarded_at += int(autoreward[5]);

				else:
				# One off reward scheme by survey count
					for survey in range(0, len(lstCompSurveys), 1):
						if (int(autoreward[5])-1 == survey):
							self.GenerateVoucher(int(autoreward[0]), autoreward[1] ,student_id, survey + 1, 'survey ONE', autoreward[7]);


	def Persist(self):
		self.LAST_SYNC_DATE = self.SurveyAPI.LAST_SYNC_DATE;
		print "PERSIST: " + self.LAST_SYNC_DATE; 
		self.dbSurvey.dbSetParam('last_sync_date', self.LAST_SYNC_DATE);


	def syncSurveys(self):
	#// Jamie Alnasir Implemented.
	#// Connect to Survey Monkey API and synchronise Surveys with RHUL Survey MySQL DB
		
		lstSurveys = self.SurveyAPI.getSurveyList();
		
		for Survey_ID in lstSurveys:
			time.sleep(2.1);
			print "getting detail for Survey_id: " + Survey_ID;
			SurveyDtl = self.SurveyAPI.getSurveyDetails(Survey_ID);
			print SurveyDtl;
			
			survey_id = long(SurveyDtl["survey_id"]);
			if self.dbSurvey.dbLogSurvey(survey_id, SurveyDtl["title"], SurveyDtl["url"], SurveyDtl["date_created"]) == 0:
				print "survey synchronised";
			else:
				print "survey NOT syncronised (SQL operation failed)";
	
	
	def syncRespondents(self):
	#// Jamie Alnasir Implemented.
	#// Connect to Survey Monkey API and synchronise Respondents for every Survey with RHUL Survey MySQL DB
		
		time.sleep(2.1);
		
		lstSurveys = self.SurveyAPI.getSurveyList();
		
		for Survey_ID in lstSurveys:
			print "checking Respondents for Survey_id: " + Survey_ID;
			
			lstRespondents = self.SurveyAPI.getSurveyRespondents(Survey_ID);
			
			print lstRespondents;
			
			for Respondent in lstRespondents:
				print Respondent["email"];
				
				time.sleep(2.5);
				
				print "STU_ID=" + Respondent["student_id"];
				
				survey_id = Survey_ID;
				
				if self.dbSurvey.dbLogCompletedSurvey(Respondent["email"], Respondent["student_id"], survey_id) == 0:
					print "respondent for survey synchronised";
				else:
					print "repondent NOT syncronised (SQL operation failed)";

	
	
# INSERT INTO tblsurveys(survey_id) VALUES (1,2,3) ON DUPLICATE KEY UPDATE c=c+1;
#print dbSurvey.dbGetParam('last_sync');

SurveyEngine = RHULSurveyEngine();

if (len(sys.argv) > 1):
	if (sys.argv[1] == "DEBUG"):
		SurveyEngine.Debug();
	elif (sys.argv[1] == "REWARDS"):
		SurveyEngine.ComputeAllRewards();
		SurveyEngine.VoucherManifest();
	elif (sys.argv[1] == "SURVEYS"):
		SurveyEngine.syncSurveys();
	elif (sys.argv[1] == "MONKEY"):
		SurveyEngine.syncSurveys();
		SurveyEngine.syncRespondents();
		SurveyEngine.Persist();
else:
	SurveyEngine.syncSurveys();
	SurveyEngine.syncRespondents();
	SurveyEngine.Persist();
	SurveyEngine.ComputeAllRewards();
	SurveyEngine.VoucherManifest();

