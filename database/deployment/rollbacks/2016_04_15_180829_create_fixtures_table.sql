ALTER TABLE `fixtures`
  DROP FOREIGN KEY `fixtures_division_id_foreign`;
ALTER TABLE `fixtures`
  DROP FOREIGN KEY `fixtures_home_team_id_foreign`;
ALTER TABLE `fixtures`
  DROP FOREIGN KEY `fixtures_away_team_id_foreign`;
ALTER TABLE `fixtures`
  DROP FOREIGN KEY `fixtures_venue_id_foreign`;
DROP TABLE `fixtures`;