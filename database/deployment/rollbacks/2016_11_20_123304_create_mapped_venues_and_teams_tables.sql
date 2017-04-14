ALTER TABLE `mapped_venues`
  DROP FOREIGN KEY `mapped_venues_upload_job_id_foreign`;
ALTER TABLE `mapped_venues`
  DROP FOREIGN KEY `mapped_venues_venue_id_foreign`;
DROP TABLE `mapped_venues`;

ALTER TABLE `mapped_teams`
  DROP FOREIGN KEY `mapped_teams_upload_job_id_foreign`;
ALTER TABLE `mapped_teams`
  DROP FOREIGN KEY `mapped_teams_team_id_foreign`;
DROP TABLE `mapped_teams`;
