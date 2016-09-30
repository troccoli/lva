ALTER TABLE `users`
  DROP INDEX `users_api_token_unique`;
ALTER TABLE `users`
  DROP `api_token`;