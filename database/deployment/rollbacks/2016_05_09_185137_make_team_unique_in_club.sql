ALTER TABLE `teams`
  DROP FOREIGN KEY `teams_club_id_foreign`;
ALTER TABLE `teams`
  DROP INDEX `unique_team_in_club`;
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`);
