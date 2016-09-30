ALTER TABLE `users`
  ADD `api_token` VARCHAR(60) NOT NULL;
ALTER TABLE `users`
  ADD UNIQUE `users_api_token_unique`(`api_token`);
