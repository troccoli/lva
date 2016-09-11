CREATE TABLE `upload_jobs` (
  `id`         INT UNSIGNED      NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `file`       VARCHAR(255)      NOT NULL,
  `type`       ENUM ('fixtures') NOT NULL,
  `status`     LONGTEXT          NOT NULL,
  `created_at` TIMESTAMP         NULL,
  `updated_at` TIMESTAMP         NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB