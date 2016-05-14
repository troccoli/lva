CREATE TABLE `fixtures` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `division_id`  INT UNSIGNED NOT NULL,
  `match_number` INT UNSIGNED NOT NULL,
  `match_date`   DATE         NOT NULL,
  `warm_up_time` TIME         NOT NULL,
  `start_time`   TIME         NOT NULL,
  `home_team_id` INT UNSIGNED NOT NULL,
  `away_team_id` INT UNSIGNED NOT NULL,
  `venue_id`     INT UNSIGNED NOT NULL,
  `created_at`   TIMESTAMP    NULL,
  `updated_at`   TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`);
ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_home_team_id_foreign` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`);
ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_away_team_id_foreign` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`);
ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`);
