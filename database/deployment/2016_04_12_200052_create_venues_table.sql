CREATE TABLE `venues` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `venue`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;