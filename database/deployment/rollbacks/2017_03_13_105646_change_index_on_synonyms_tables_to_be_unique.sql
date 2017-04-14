ALTER TABLE `teams_synonyms`
  DROP INDEX `teams_synonyms_synonym_unique`;
ALTER TABLE `teams_synonyms`
  ADD INDEX `teams_synonyms_synonym_index`(`synonym`);
ALTER TABLE `venues_synonyms`
  DROP INDEX `venues_synonyms_synonym_unique`;
ALTER TABLE `venues_synonyms`
  ADD INDEX `venues_synonyms_synonym_index`(`synonym`);