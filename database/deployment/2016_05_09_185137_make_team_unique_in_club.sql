ALTER TABLE `teams`
  ADD UNIQUE `unique_team_in_club`(`club_id`, `team`);