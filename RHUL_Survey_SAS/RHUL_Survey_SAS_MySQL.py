#//==============================================================================
#// RHUL Survey Project - SurveyMonkey API Synchronisation System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for Economics Department
#// Copyright (c) 2014 Jamie J. Alnasir, All Rights Reserved
#//==============================================================================
#// Version: Python edition
#//==============================================================================

import MySQLdb;
import time;

_DB_HOST = 'localhost';
_DB_USER = 'choosesurveypythonuser';
_DB_PASS = 'password'
_DB_NAME = 'choosesurveypythondb';

def addslashes(s):
# Implementation of PHPs addslashes function
# from http://www.php2python.com/wiki/function.addslashes/
	l = ["\\", '"', "'", "\0", ];
	for i in l:
		if i in s:
			s = s.replace(i, '\\'+i);
	return s;


class RHULSurveyDB:
	
	POINTS_PER_SURVEY = "";

	def setPointsPerSurvey(self, points):
                self.POINTS_PER_SURVEY = points;


	def dbGetParam(self, param_name):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and fetch a global parameter from the tblsurvey_global table

		result = "";
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			# prepare a cursor object using cursor() method
			cursor = db.cursor();

			# execute SQL query using execute() method.
			cursor.execute("SELECT value FROM tblsurvey_global where param='" + param_name + "';");

			# Fetch a single row using fetchone() method.
			data = cursor.fetchone();

			if (data is None):
				result = "";
			else:
				result = data[0];
		
		finally:
			# disconnect from server
			db.close();
		return result;
		
	def dbSetParam(self, param_name, param_value):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and set a global parameter in the tblsurvey_global table

		result = -1;
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			cursor = db.cursor();

			# Prepare SQL query to DELETE existing parameter record from the database
			# prior to INSERT
			sql = "DELETE FROM tblsurvey_global WHERE lower(param) = '" + param_name + "'";
			try:
				# Execute the SQL command
				cursor.execute(sql);
				# Commit your changes in the database
				db.commit()
				result = 0;
			except MySQLdb.Error, e:
				# Rollback in case there is any error
				db.rollback()
				result = -1;
				print e;
			
			# Prepare SQL query to INSERT a record into the database.
			sql = "INSERT INTO tblsurvey_global (param, value) \
				   VALUES ('%s', '%s')" % \
				   (param_name, param_value)
			try:
				# Execute the SQL command
				cursor.execute(sql);
				# Commit your changes in the database
				db.commit()
				result = 0;
			except MySQLdb.Error, e:
				# Rollback in case there is any error
				db.rollback()
				result = -1;
				print e;
		
		finally:
			# disconnect from server
			db.close();
			
		return result;

		
	def dbLogSurvey(self, survey_id, title, url, date_created):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and IF NECESSARY add the survey to the DB

		result = -1;
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			cursor = db.cursor();

			title = addslashes(title);
			
			# Prepare SQL query to INSERT a record into the database.
			sql = "INSERT INTO tblsurveys (survey_id, title, url, date_created) \
				   VALUES ('%d', '%s', '%s', '%s')" % \
				   (survey_id, title, url, date_created)
			try:
				# Execute the SQL command
				cursor.execute(sql);
				# Commit your changes in the database
				db.commit()
				result = 0;
			except MySQLdb.Error, e:
				# Rollback in case there is any error
				db.rollback()
				result = -1;
				print e;
		
		finally:
			# disconnect from server
			db.close();
			
		return result;
	
	
	def dbLogCompletedSurvey(self, respondent_email, student_id, survey_id):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and record a given survey as being completed
	#// by given respondent_email

		result = -1;
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			cursor = db.cursor();
			
			print "STU_ID2LOG=" + student_id;
			print "SURVEY POINTS AWARDED:" + self.POINTS_PER_SURVEY;
			
			date_time = time.strftime("%d/%m/%Y %H:%M:%S");

			# Prepare SQL query to INSERT a record into the database.
			sql = "INSERT IGNORE INTO tblsurveys_completed (email, student_id, survey_id, date, points) \
				   VALUES ('%s', '%s', '%s', '%s', '%d')" % \
				   (respondent_email, student_id, survey_id, date_time , int(self.POINTS_PER_SURVEY))
			try:
				# Execute the SQL command
				cursor.execute(sql);
				# Commit your changes in the database
				db.commit()
				result = 0;
			except MySQLdb.Error, e:
				# Rollback in case there is any error
				db.rollback()
				result = -1;
				print e;
		
		finally:
			# disconnect from server
			db.close();
			
		return result;
	

	def dbGetStudentList(self):
        #// Jamie Alnasir Implemented.
        #// Connect to RHUL Survey MySQL DB and fetch a list of ALL students who have completed something

                result = None;
                try:
                        # Open database connection
                        db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

                        # prepare a cursor object using cursor() method
                        cursor = db.cursor();

                        # execute SQL query using execute() method.
                        cursor.execute("SELECT DISTINCT(student_id) FROM tblsurveys_completed");

                        # Fetch multiple rows
                        data = cursor.fetchall();

                        if (data is None):
                                result = None;
                        else:
                                result = data;

                finally:
                        # disconnect from server
                        db.close();

                return result;


	def dbGetCompletedSurveyList(self, student_id):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and fetch a list of completed surveys for given student

		result = None;
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			# prepare a cursor object using cursor() method
			cursor = db.cursor();

			# execute SQL query using execute() method.
			cursor.execute("SELECT * FROM tblsurveys_completed where student_id = '" + student_id + "'");

			# Fetch multiple rows
			data = cursor.fetchall();
			
			if (data is None):
				result = None;
			else:
				result = data;
		
		finally:
			# disconnect from server
			db.close();
			
		return result;

	def dbGetAutoRewardList(self):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and fetch a list of auto-reward schemes

		result = None;
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			# prepare a cursor object using cursor() method
			cursor = db.cursor();

			# execute SQL query using execute() method.
			cursor.execute("SELECT * FROM tblsurvey_auto_rewards");

			# Fetch multiple rows
			data = cursor.fetchall();
			
			if (data is None):
				result = None;
			else:
				result = data;
		
		finally:
			# disconnect from server
			db.close();
			
		return result;

        def dbGetVoucherList(self):
        #// Jamie Alnasir Implemented.
        #// Connect to RHUL Survey MySQL DB and fetch a list of ALL vouchers

                result = None;
                try:
                        # Open database connection
                        db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

                        # prepare a cursor object using cursor() method
                        cursor = db.cursor();

                        # execute SQL query using execute() method.
                        cursor.execute("SELECT * FROM tblsurvey_vouchers");

                        # Fetch a single row using fetchone() method.
                        data = cursor.fetchall();

                        if (data is None):
                                result = None;
                        else:
                                result = data;

                finally:
                        # disconnect from server
                        db.close();

                return result;

	
	def dbRewardVoucher(self, reward_type_id, student_id, awarded_at, awarded_by, issue_date, voucher_code, expiry_date):
	#// Jamie Alnasir Implemented.
	#// Connect to RHUL Survey MySQL DB and issue an reward voucher

		result = -1;
		try:
			# Open database connection
			db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

			cursor = db.cursor();
			
			print "AWARD VOUCHER, TYPE=" + str(reward_type_id);
			print "STU_ID=" + student_id;
			print "AWARDED AT: " + str(awarded_at);
			print "AWARDED BY: " + awarded_by;
			print "EXPIRES: " + expiry_date;
			
			# Prepare SQL query to INSERT a record into the database.
			sql = "INSERT INTO tblsurvey_vouchers (reward_type_id, student_id, awarded_at, awarded_by, issue_date, voucher_code) \
				   VALUES ('%d', '%s', '%d', '%s', '%s', '%s')" % \
				   (reward_type_id, student_id, awarded_at, awarded_by, issue_date, voucher_code)
			try:
				# Execute the SQL command
				cursor.execute(sql);
				# Commit your changes in the database
				db.commit()
				result = cursor.lastrowid; # Return auto-increment ID field
			except MySQLdb.Error, e:
				# Rollback in case there is any error
				db.rollback()
				result = -1;
				print e;
		
		finally:
			# disconnect from server
			db.close();
			
		return result;

	def dbGetVoucherFastCode(self, voucher_code):
        #// Jamie Alnasir Implemented.
        #// Connect to RHUL Survey MySQL DB and fetch auto-incremented id field to use as voucher-fast-code lookup

                result = "";
                try:
                        # Open database connection
                        db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

                        # prepare a cursor object using cursor() method
                        cursor = db.cursor();

                        # execute SQL query using execute() method.
                        cursor.execute("SELECT id FROM tblsurvey_vouchers where voucher_code='" + voucher_code + "';");

                        # Fetch a single row using fetchone() method.
                        data = cursor.fetchone();

                        if (data is None):
                                result = "";
                        else:
                                result = data[0];

                finally:
                        # disconnect from server
                        db.close();
                return result;


	def dbGetGCMList(self):
        #// Jamie Alnasir Implemented.
        #// Connect to RHUL Survey MySQL DB and fetch a list of ALL Google registered devices

                result = None;
                try:
                        # Open database connection
                        db = MySQLdb.connect(host=_DB_HOST,user=_DB_USER,passwd=_DB_PASS,db=_DB_NAME);

                        # prepare a cursor object using cursor() method
                        cursor = db.cursor();

                        # execute SQL query using execute() method.
                        cursor.execute("SELECT pretty_id, reg_id, ip, date FROM tblgcm_reg");

                        # Fetch multiple rows
                        data = cursor.fetchall();

                        if (data is None):
                                result = None;
                        else:
                                result = data;

                finally:
                        # disconnect from server
                        db.close();

                return result;


