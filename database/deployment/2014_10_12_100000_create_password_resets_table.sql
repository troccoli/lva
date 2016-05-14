
CREATE TABLE `password_resets` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE `password_resets`
  ADD INDEX `password_resets_email_index`(`email`);
ALTER TABLE `password_resets`
  ADD INDEX `password_resets_token_index`(`token`);
