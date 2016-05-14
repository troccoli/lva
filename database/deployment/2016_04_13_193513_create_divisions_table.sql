CREATE TABLE `divisions` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `season_id`  INT UNSIGNED NOT NULL,
  `division`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `divisions`
  ADD CONSTRAINT `divisions_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`);