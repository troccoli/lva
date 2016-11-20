CREATE TABLE `venues_synonyms` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `synonym`    VARCHAR(255) NOT NULL,
  `venue_id`   INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `venues_synonyms`
  ADD INDEX `venues_synonyms_synonym_index`(`synonym`);
ALTER TABLE `venues_synonyms`
  ADD INDEX `venues_synonyms_venue_id_index`(`venue_id`);
ALTER TABLE `venues_synonyms`
  ADD CONSTRAINT `venues_synonyms_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`);

CREATE TABLE `teams_synonyms` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `synonym`    VARCHAR(255) NOT NULL,
  `team_id`    INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `teams_synonyms`
  ADD INDEX `teams_synonyms_synonym_index`(`synonym`);
ALTER TABLE `teams_synonyms`
  ADD INDEX `teams_synonyms_team_id_index`(`team_id`);
ALTER TABLE `teams_synonyms`
  ADD CONSTRAINT `teams_synonyms_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);
