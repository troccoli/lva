CREATE TABLE `users` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name`           VARCHAR(255) NOT NULL,
  `email`          VARCHAR(255) NOT NULL,
  `password`       VARCHAR(60)  NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at`     TIMESTAMP    NULL,
  `updated_at`     TIMESTAMP    NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `users`
  ADD UNIQUE `users_email_unique`(`email`);
