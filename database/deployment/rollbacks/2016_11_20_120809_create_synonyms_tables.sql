ALTER TABLE `venues_synonyms`
  DROP FOREIGN KEY `venues_synonyms_venue_id_foreign`;
DROP TABLE `venues_synonyms`;

ALTER TABLE `teams_synonyms`
  DROP FOREIGN KEY `teams_synonyms_team_id_foreign`;
DROP TABLE `teams_synonyms`;