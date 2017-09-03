#//==============================================================================
#// RHUL Survey Project - SurveyMonkey API Synchronisation System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for Economics Department
#// Copyright (c) 2014 Jamie J. Alnasir, All Rights Reserved
#//==============================================================================
#// Version: Python edition
#//==============================================================================


import requests;
import json;
import time;
import pprint;

#SURVEY_ID = "62891378";
HOST = "https://api.surveymonkey.net";

# set up the two endpoint urls
surveylist_uri = "%s/v2/surveys/get_survey_list" % HOST
surveydtl_uri = "%s/v2/surveys/get_survey_details" % HOST
respondent_uri = "%s/v2/surveys/get_respondent_list" % HOST
response_uri = "%s/v2/surveys/get_responses" % HOST
collector_uri = "%s/v2/surveys/get_collector_list" % HOST


def sleep(time_sec):
		#time.sleep(time_sec);
		time.sleep(1);


class RHULSurveyMonkeyAPI:

	client = requests.session()
	LAST_SYNC_DATE = "2013-01-01 00:00:00"; # Default value (Performs whole sync)
	POINTS_PER_SURVEY = "";

	def __init__(self):
	
		# ChooseRHUL SurveyMonkey Account
		API_KEY = "YOUR_SURVEYMONKEY_API_KEY_HERE";		
		USER_ACCESS_TOKEN = "YOUR_SURVEYMONKEY_API_ACCESS_TOKEN_HERE";
		
		 		
		self.client.headers = {
			"Authorization": "bearer %s" % USER_ACCESS_TOKEN,
			"Content-Type": "application/json"
		}
		self.client.params = {
			"api_key": API_KEY
		}
		print "connecting to " + HOST;	
		
	def setLastSyncDate(self, date):
		self.LAST_SYNC_DATE = date;

	def setPointsPerSurvey(self, points):
		self.POINTS_PER_SURVEY = points;

	def getSurveyList(self):
	#// Jamie Alnasir Implemented.
	#// Connect to SurveyMonkey API to fetch a list of Survey IDs
    #// returned as an array

		result_surveylist = [];

		# set up the initial data for surveylist response
		surveylist_post_data = {};
		surveylist_response_data = {};		

		surveylist_cur_page = 1
		while True:
			surveylist_post_data["page"] = surveylist_cur_page;
			surveylist_response_data = self.client.post(surveylist_uri, data=json.dumps(surveylist_post_data));
			surveylist_json = surveylist_response_data.json();

			#print surveylist_json;

			if len(surveylist_json["data"]["surveys"]) == 0:
				print "done";
				break;
			
		 
			survey_ids = []
			for survey in surveylist_json["data"]["surveys"]:
				result_surveylist.append(survey["survey_id"]);
				print survey;
				# keep track of the max modified date
				#max_modified_respondent_date = survey["date_modified"]
		 
	 
			# get_responses can only take in 100 respondent_ids, we need to 
			# batch these requests in chunks of 100
			#start_pos = 0
			#survey_count = len(survey_ids)
			#while start_pos < survey_count:
			#	response_post_data["respondent_ids"] = respondent_ids[start_pos:start_pos + 100]
			#	response_data = client.post(response_uri, data=json.dumps(response_post_data))
			#	response_json = response_data.json()
			#	for response in response_json["data"]:
			#	    output_response_list.append(response)
			#	start_pos += 100

			surveylist_cur_page += 1		
		 
		# update LAST_DATE_CHECKED
		#self.LAST_SYNC_DATE = max_modified_respondent_date
		
		return result_surveylist;


	def getSurveyDetails(self, survey_id):
	#// Jamie Alnasir Implemented.
	#// Connect to SurveyMonkey API to fetch a basic details of a given Survey ID
    #// returned as a dict

		result_surveydtl = {};
		sleep(2.5);		

		# set up the initial data for surveylist response
		surveydtl_post_data = {};
		surveydtl_response_data = {};

		surveydtl_post_data["survey_id"] = survey_id;

		surveydtl_response_data = self.client.post(surveydtl_uri, data=json.dumps(surveydtl_post_data));
		surveydtl_json = surveydtl_response_data.json();

		#print surveydtl_json;
		#print "\n";
		#print surveydtl_json["data"]["title"]["text"] + "\n";

		result_surveydtl["survey_id"] = survey_id;		
		result_surveydtl["title"] = surveydtl_json["data"]["title"]["text"];
		result_surveydtl["date_created"] = surveydtl_json["data"]["date_created"];
		result_surveydtl["question_count"] = surveydtl_json["data"]["question_count"];
		
		sleep(2.5);
		result_surveydtl["url"] = self.getSurveyURL(survey_id);
		
		return result_surveydtl;


	def getSurveyURL(self, survey_id):
	#// Jamie Alnasir Implemented.
	#// Connect to SurveyMonkey API to fetch URL of a given Survey ID
    #// returned as a string

	#// Jamie Alnasir Implemented.
	#// Connect to SurveyMonkey API to fetch a list of Survey IDs
    #// returned as an array

		result = "";

		# set up the initial data for surveylist response
		surveyurl_post_data = {};
		surveyurl_response_data = {};
		surveyurl_post_data["survey_id"] = survey_id;
		surveyurl_post_data["fields"] = ["url"];

		surveyurl_cur_page = 1
		while True:
			surveyurl_post_data["page"] = surveyurl_cur_page;
			surveyurl_response_data = self.client.post(collector_uri, data=json.dumps(surveyurl_post_data));
			surveyurl_json = surveyurl_response_data.json();

			#print surveyurl_json;

			if len(surveyurl_json["data"]["collectors"]) == 0:
				#print "done";
				break;
			
		 
			for collector in surveyurl_json["data"]["collectors"]:
				#print collector["url"];
				if (collector["url"]):
					result = collector["url"];
		 
			surveyurl_cur_page += 1		
		 
		# update LAST_DATE_CHECKED
		# self.LAST_SYNC_DATE = max_modified_respondent_date
		
		return result;



	def getSurveyRespondents(self, survey_id):
	#// Jamie Alnasir Implemented.
	#// Connect to SurveyMonkey API to fetch URL of a given Survey ID
    #// returned as a string

	#// Jamie Alnasir Implemented.
	#// Connect to SurveyMonkey API to fetch a list of Survey IDs
    #// returned as an array
		
		result_list = [];
		sleep(2.5);

		# set up the initial post data for respondent and responses
		# endpoints
		respondent_post_data = {};
		respondent_post_data["survey_id"] = survey_id;
		respondent_post_data["fields"] = ["date_modified","status","email", "custom_id", "first_name", "last_name"];
		respondent_post_data["start_modified_date"] = self.LAST_SYNC_DATE;
		response_post_data = {};
		response_post_data["survey_id"] = survey_id;

		# max modified date encountered
		max_modified_respondent_date = self.LAST_SYNC_DATE;
 
		# final responses output
		output_response_list = [];
 
		respondents_cur_page = 1
		while True:
			sleep(2.5);
			respondent_post_data["page"] = respondents_cur_page;
			respondent_data = self.client.post(respondent_uri, data=json.dumps(respondent_post_data));
			respondent_json = respondent_data.json();

			print respondent_data;

			if len(respondent_json["data"]["respondents"]) == 0:
				break;
 
			respondent_ids = [];
			for respondent in respondent_json["data"]["respondents"]:
			 
				# keep track of the max modified date
				if respondent["date_modified"] > max_modified_respondent_date:
					max_modified_respondent_date = respondent["date_modified"];
				 
				result_respondent = {};

				# ** CRITICAL ** only want finished responses
				if respondent["status"] == "completed":
					respondent_ids.append(respondent["respondent_id"]);
					#print respondent["email"];

					result_respondent["respondent_id"] = respondent["respondent_id"];
					result_respondent["student_id"] = respondent["custom_id"];
					result_respondent["email"] = respondent["email"];
					#result_respondent["first_name"] = respondent["first_name"]
					#result_respondent["last_name"] = respondent["last_name"]
					result_list.append(result_respondent);

			respondents_cur_page += 1;
 
			# update LAST_DATE_CHECKED
			self.LAST_SYNC_DATE = max_modified_respondent_date;

			print "--->" + self.LAST_SYNC_DATE;

		return result_list;





