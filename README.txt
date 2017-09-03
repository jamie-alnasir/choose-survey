
Choose-survey respondent tracking system

Architecture/Software developed by Jamie Alnasir, Project conceieved by the department of
Economics, Royal Holloway University of London


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

./survey-www/wp-content/themes/choose-survey/*		Main Wordpress driven system for user login, displaying surveys (Wordpress/PHP/MySQL)
./choose-admin/*		Choose-Survey administration panel (PHP/MySQL)
./RHUL_Survey_SAS/*		Choose-Survey Synchronisation and Voucher system (Python/MySQL)
./survey-www/push/*		Push notifications system (PHP/MySQL/GCM/Firebase)

