#//==============================================================================
#// RHUL Choose-Survey Project - PHP/MySQL Survey Respondent Tracking System
#// By Jamie Alnasir, 04/2015
#// Royal Holloway University of London
#// Dept. Computer Science for the Economics Department
#// Copyright (c) 2015 Jamie J. Alnasir, All Rights Reserved
#//==============================================================================
#// Version: MySQL DDL/SQL Schema
#//==============================================================================

# This script contains the DDL/SQL statements necessary to create the underlying MySQL DB Scheme
# for the Choose-Survey Respondent tracking system


CREATE TABLE `tblsurvey_global` (
  `param` varchar(30) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Persistant parameters for the RHUL Survey Backend';


CREATE TABLE `tblsurveys` (
  `survey_id` int(10) unsigned NOT NULL,
  `title` varchar(256) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `date_created` varchar(45) DEFAULT NULL,
  `target_year` int(4) DEFAULT NULL,
  PRIMARY KEY (`survey_id`),
  UNIQUE KEY `SurveyID_UNIQUE` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


delimiter //
CREATE TRIGGER trgsurvey_vis BEFORE INSERT ON tblsurveys
     FOR EACH ROW
     BEGIN
         IF (NEW.hidden = '') OR (NEW.hidden IS NULL) THEN
             SET NEW.hidden='Y';
         END IF;
    END;//
delimiter ;



CREATE TABLE `tblsurveys_completed` (
  `email` varchar(100) NOT NULL DEFAULT '',
  `survey_id` int(10) unsigned NOT NULL,
  `date` varchar(100) DEFAULT NULL,
  `student_id` varchar(10) DEFAULT NULL,
  `points` int DEFAULT NULL
  PRIMARY KEY (`survey_id`, `student_id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

NB: Primary key of PRIMARY KEY (`survey_id`, `student_id`) is critical to
utilise referential integrity to ensure completed surveys are not duplicated
despite multiple synchronisations.



CREATE TABLE `tblsurvey_msgs` (
  `msg_id` INT NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(80) NOT NULL,
  `body` TEXT NOT NULL,
  PRIMARY KEY (`msg_id`),
  UNIQUE INDEX `msg_id_UNIQUE` (`msg_id` ASC));


  
  CREATE TABLE `tblsurvey_auto_rewards` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(80) NOT NULL,
  `display_name` VARCHAR(80) NOT NULL,
  `type` VARCHAR(4) NOT NULL,
  `point_occur` INT,
  `survey_occur` INT,
  `start_at_one` VARCHAR(1),
  `expiry_date` varchar(100) DEFAULT NULL,
  `terms` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));



delimiter //
CREATE TRIGGER trgauto_reward_expiry BEFORE INSERT ON tblsurvey_auto_rewards
     FOR EACH ROW
     BEGIN
         IF (NEW.expiry_date = '') OR (NEW.expiry_date IS NULL) THEN
             SET NEW.expiry_date=DATE_FORMAT(TIMESTAMPADD(DAY,42,NOW()), '%d/%m/%Y');
         END IF;
    END;//
 delimiter ;

  
  insert into tblsurvey_auto_rewards (title, display_name, type, survey_occur) VALUES ('coffee', 'coffee', 'R', 3);
  insert into tblsurvey_auto_rewards (title, display_name, type, survey_occur) VALUES ('trigtest', 'trigger test!', 'R', 3);
  
  
  CREATE TABLE `tblsurvey_vouchers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `reward_type_id` INT NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `awarded_at` INT NOT NULL,
  `awarded_by` VARCHAR(10) NOT NULL,
  `voucher_code` VARCHAR(80) NOT NULL,
  `issue_date` varchar(100) DEFAULT NULL,
  `redeem_date` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`reward_type_id`, `awarded_at`, `student_id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));
  
NB: Primary key of PRIMARY KEY (`reward_type_id`, `awarded_at`, `student_id`) is critical to
utilise referential integrity to ensure vouchers are not duplicated despite multiple
synchronisations.
  

CREATE TABLE `tblsurvey_reports` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(80) NOT NULL,
  `col_fields` VARCHAR(100) DEFAULT NULL,
  `sql` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC));  
  
  
CREATE VIEW vwvouchers AS SELECT a.student_id, a.issue_date, a.redeem_date, b.display_name, a.voucher_code, b.expiry_date, b.terms from tblsurvey_vouchers a, tblsurvey_auto_rewards b
where a.reward_type_id = b.id;

CREATE VIEW  vwsurveys_completed AS SELECT student_id, count(survey_id) completed, sum(points) total_points from tblsurveys_completed group by student_id;  
  
CREATE VIEW vwsurveys_completed_extent as select completed, count(completed) from vwsurveys_completed group by completed;

CREATE VIEW vwsurveys_completed_dtl as select c.student_id, p.title, c.date date_completed, c.points points_awarded from tblsurveys_completed c, tblsurveys p where c.survey_id = p.survey_id order by student_id, title;

NB: For google cloud messenger push notification device registration_ids

CREATE TABLE `tblgcm_reg`
(
  `reg_id` VARCHAR(256) UNIQUE NOT NULL,
  `pretty_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` VARCHAR(16) NOT NULL,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pretty_id`)
);

NB: start pretty_id from 1000
ALTER TABLE tblgcm_reg AUTO_INCREMENT=1001;


CREATE TABLE `tblgcm_msg_log`
(
  `msg_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subj` VARCHAR(32) NOT NULL,
  `msg` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`msg_id`)
);

ALTER TABLE tblgcm_msg_log AUTO_INCREMENT=1000;


-- Trigger to automatically copy GCM Push notification message into survey message table.

delimiter //
CREATE TRIGGER trgpushmsgs AFTER INSERT ON tblgcm_msg_log
FOR EACH ROW
BEGIN
  INSERT INTO tblsurvey_msgs (subject, body) VALUES (new.subj, new.msg);
END;//
delimiter ;


IMPORTANT -- THIS IS A VIEW THAT MUST BE ADDED TO THE SEPARATE WORDPRESS DATABASE WHICH IS CONNECTED WITH LDAP...

NB: Start year is extracted from the student's display name (will be null if non-student)

CREATE VIEW vw_rhul_students
AS
SELECT
  user_id,
  MAX(IF(meta_key = 'adi_studentno', meta_value, NULL)) AS student_id,
  MAX(IF(meta_key = 'adi_samaccountname', meta_value, NULL)) AS it_account,
  MAX(IF(meta_key = 'first_name', meta_value, NULL)) AS first_name,
  MAX(IF(meta_key = 'last_name', meta_value, NULL)) AS last_name,
  MAX(IF(meta_key = 'adi_displayname', meta_value, NULL)) AS display_name,
  MAX(IF(meta_key = 'adi_mail', meta_value, NULL)) AS email,
  MAX(IF(meta_key = 'adi_displayname', IF(instr(meta_value, '('), substr(meta_value,instr(meta_value, '(')+1,4), NULL), NULL)) AS start_year
FROM
	cswp_usermeta
WHERE meta_key IN ('first_name', 'last_name', 'adi_displayname', 'adi_mail', 'adi_samaccountname', 'adi_studentno')
GROUP BY
  user_id;


-- NB: The vw_rhul_consent view must be created every year so as to include
-- all the previous backed up survey data (tblsurveys_completed_[yr])
  
CREATE VIEW vw_rhul_consent
AS
SELECT distinct(student_id), date
FROM tblsurveys_completed
WHERE survey_id IN
(
  SELECT survey_id FROM tblsurveys WHERE title like "%consent%"
) UNION
SELECT distinct(student_id), date
FROM tblsurveys_completed_2015
WHERE survey_id IN
(
  SELECT survey_id FROM tblsurveys WHERE title like "%consent%"
);


