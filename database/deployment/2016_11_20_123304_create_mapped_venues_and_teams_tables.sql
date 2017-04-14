CREATE TABLE `mapped_venues` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `upload_job_id` INT UNSIGNED NOT NULL,
  `venue`         VARCHAR(255) NOT NULL,
  `venue_id`      INT UNSIGNED NOT NULL,
  `created_at`    TIMESTAMP    NULL,
  `updated_at`    TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `mapped_venues`
  ADD INDEX `mapped_venues_venue_index`(`venue`);
ALTER TABLE `mapped_venues`
  ADD CONSTRAINT `mapped_venues_upload_job_id_foreign` FOREIGN KEY (`upload_job_id`) REFERENCES `upload_jobs` (`id`);
ALTER TABLE `mapped_venues`
  ADD CONSTRAINT `mapped_venues_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`);

CREATE TABLE `mapped_teams` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `upload_job_id` INT UNSIGNED NOT NULL,
  `team`          VARCHAR(255) NOT NULL,
  `team_id`       INT UNSIGNED NOT NULL,
  `created_at`    TIMESTAMP    NULL,
  `updated_at`    TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `mapped_teams`
  ADD INDEX `mapped_teams_team_index`(`team`);
ALTER TABLE `mapped_teams`
  ADD CONSTRAINT `mapped_teams_upload_job_id_foreign` FOREIGN KEY (`upload_job_id`) REFERENCES `upload_jobs` (`id`);
ALTER TABLE `mapped_teams`
  ADD CONSTRAINT `mapped_teams_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);
