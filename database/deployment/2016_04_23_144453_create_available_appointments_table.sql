CREATE TABLE `available_appointments` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fixture_id` INT UNSIGNED NOT NULL,
  `role_id`    INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `available_appointments`
  ADD UNIQUE `available_appointments_fixture_id_role_id_unique`(`fixture_id`, `role_id`);

ALTER TABLE `available_appointments`
  ADD CONSTRAINT `available_appointments_fixture_id_foreign` FOREIGN KEY (`fixture_id`) REFERENCES `fixtures` (`id`);
ALTER TABLE `available_appointments`
  ADD CONSTRAINT `available_appointments_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);