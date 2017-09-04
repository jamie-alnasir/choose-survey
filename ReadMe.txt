
Choose-survey respondent tracking system

Architecture/Software developed by Jamie Alnasir, Project conceieved by the department of
Economics (Anderberg, Dan ; Chevalier, Arnaud ; Luhrmann, Melanie ; Pavan, Ronni),
Royal Holloway University of London


Choose Survey Project:

The Choose-Survey project is presented at the project website:
https://www.royalholloway.ac.uk/economics/choosesurvey/choosesurvey.aspx

An abstract of the software system can be found on the Royal Holloway academic server:
https://pure.royalholloway.ac.uk/portal/en/publications/choosesurvey(fe679290-fb48-436b-ab59-1de49b1caa8c).html

Technical implementation of the project is discussed here:
http://www.al-nasir.com/portfolio/choose-survey/



Overview of Architecture:

The architecture of the system for survey response tracking (SurveyMonkey, Wordpress
and LDAP integration) as well as push notifications can be found in choose-survey.jpg
and choose-survey-push.jpg, respectively. Please view these first!

The student/user login system, to display a seletion of surveys in the SurveyMonkey account is
is built on a wordpress theme (choose-survey) and is written in PHP/MySQL. The client side
apps (written in Android and iOS) allow student users to log-into the Wordpress Choose-Survey
system by means of a "thin client", view surveys, and recieve push-notifications. The push-notifications
is the only other client-side code for what is essentially a Webview in the client, and allows
devices to recieve push-notifications whilst not-logged into the site (through asynchronous devices
communication via Google cloud messaging).

The synchronisation system is written in Python/MySQL and synchronises SurveyMonkey and Choose-Survey
metadata so as to keep track of which students have completed which surveys, and then can also generate
vouchers to redeem for gifts (i.e. coffee at the university cafe). The administration panel, written in
PHP/MySQL, allows creation/admin of rewards schemes, administration of survey completion metadata,
reports of survey completion and sending of push notifications. The push-notifications system, written in PHP/MySQL/GCM/Firebase is used by the control panel to send notifications to registered devices.


** The code provided herein has not been modified for external release, but only to remove
authentication details. Paths to server hosted files, MySQL databases and configuration
is necessary, as is building/compiling of the Android/iOS client apps.

Folder structure:

[Server-side code]
./survey-www/wp-content/themes/choose-survey/*		Main Wordpress driven system for user login, displaying surveys (Wordpress/PHP/MySQL)
./choose-admin/*			Choose-Survey administration panel (PHP/MySQL)
./RHUL_Survey_SAS/*			Choose-Survey Synchronisation and Voucher system (Python/MySQL)
./survey-www/push/*			Push notifications system (PHP/MySQL/GCM/Firebase)

[Client-side code]
./Android.client.app/*		Android GCM Push-notifications enabled Webview "Thin" Client
./iOS.client.app/*			iOS GCM/Firebase Push-notifications enabled Webview "Thin" Client


MySQL Database Schema:

The server-side code is built on Wordpress, and therefore requires a Wordpress MySQL instance as well
as a separate MySQL instance for storing the survey respondent metadata (This can be be the same database
as the Wordpress MySQL instance if desired).

The Schema DDL/SQL script containing the necessary table, view, trigger definitions is in MySQL-Schema.sql.


Additional notes:

The metadata system currently only keeps track of the surveys in SurveyMonkey together with the respondents
(students) that complete the surveys in order to only show new, un-completed surveys and to generate gamification
points for voucher generation. The Syncronisation system, written in Python, can be used to also iterate the
survey respondent data to capture and store this in the metadata if so required.

