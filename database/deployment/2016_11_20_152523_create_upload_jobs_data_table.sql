CREATE TABLE `upload_jobs_data` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `upload_job_id` INT UNSIGNED NOT NULL,
  `model`         VARCHAR(255) NOT NULL,
  `model_data`    TEXT         NOT NULL,
  `created_at`    TIMESTAMP    NULL,
  `updated_at`    TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `upload_jobs_data`
  ADD CONSTRAINT `upload_jobs_data_upload_job_id_foreign` FOREIGN KEY (`upload_job_id`) REFERENCES `upload_jobs` (`id`);