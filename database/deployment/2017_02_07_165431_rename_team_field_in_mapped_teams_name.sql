ALTER TABLE `mapped_teams`
  DROP INDEX `mapped_teams_team_index`;
ALTER TABLE mapped_teams
  CHANGE team mapped_team VARCHAR(255) NOT NULL;
ALTER TABLE `mapped_teams`
  ADD INDEX `mapped_teams_mapped_team_index`(`mapped_team`);
