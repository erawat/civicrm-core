{* file to handle db changes in 5.0.2 during upgrade *}

ALTER TABLE civicrm_case_type
ADD COLUMN category VARCHAR(512);
