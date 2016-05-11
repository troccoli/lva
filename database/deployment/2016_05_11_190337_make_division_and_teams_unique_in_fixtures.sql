ALTER TABLE `fixtures`
  ADD UNIQUE `unique_division_and_teams`(`division_id`, `home_team_id`, `away_team_id`);