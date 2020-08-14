{* file to handle db changes in 5.30.0 during upgrade *}

ALTER TABLE civicrm_mail_settings
ADD is_non_case_email_skipped TINYINT default 0 not null comment 'Skip emails which do not have a Case ID or Case token';
