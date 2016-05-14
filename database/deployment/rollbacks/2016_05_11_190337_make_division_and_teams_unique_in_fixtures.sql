ALTER TABLE `fixtures`
  DROP FOREIGN KEY `fixtures_division_id_foreign`;
ALTER TABLE `fixtures`
  DROP INDEX `unique_division_and_teams`;
ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`);