ALTER TABLE `divisions`
  DROP FOREIGN KEY `divisions_season_id_foreign`;
ALTER TABLE `divisions`
  DROP INDEX `unique_division`;
ALTER TABLE `divisions`
  ADD CONSTRAINT `divisions_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`);