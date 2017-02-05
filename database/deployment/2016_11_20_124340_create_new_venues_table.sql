CREATE TABLE `new_venues` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `upload_job_id` INT UNSIGNED NOT NULL,
  `venue`         VARCHAR(255) NOT NULL,
  `venue_id`      INT UNSIGNED NULL,
  `created_at`    TIMESTAMP    NULL,
  `updated_at`    TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `new_venues`
  ADD INDEX `new_venues_venue_index`(`venue`);
ALTER TABLE `new_venues`
  ADD CONSTRAINT `new_venues_upload_job_id_foreign` FOREIGN KEY (`upload_job_id`) REFERENCES `upload_jobs` (`id`);
