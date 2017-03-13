ALTER TABLE `teams_synonyms`
  DROP INDEX `teams_synonyms_synonym_index`;
ALTER TABLE `teams_synonyms`
  ADD UNIQUE `teams_synonyms_synonym_unique`(`synonym`);
ALTER TABLE `venues_synonyms`
  DROP INDEX `venues_synonyms_synonym_index`;
ALTER TABLE `venues_synonyms`
  ADD UNIQUE `venues_synonyms_synonym_unique`(`synonym`);
