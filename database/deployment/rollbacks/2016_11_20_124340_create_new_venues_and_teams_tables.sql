ALTER TABLE `new_venues`
  DROP FOREIGN KEY `new_venues_upload_job_id_foreign`;
DROP TABLE `new_venues`;

ALTER TABLE `new_teams`
  DROP FOREIGN KEY `new_teams_upload_job_id_foreign`;
DROP TABLE `new_teams`;
