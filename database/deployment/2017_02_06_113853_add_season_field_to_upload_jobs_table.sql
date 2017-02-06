ALTER TABLE `upload_jobs`
  ADD `season_id` INT UNSIGNED NOT NULL;
ALTER TABLE `upload_jobs`
  ADD CONSTRAINT `upload_jobs_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`);