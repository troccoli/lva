ALTER TABLE `mapped_venues`
  DROP INDEX `mapped_venues_mapped_venue_index`;
ALTER TABLE mapped_venues
  CHANGE mapped_venue venue VARCHAR(255) NOT NULL;
ALTER TABLE `mapped_venues`
  ADD INDEX `mapped_venues_venue_index`(`venue`);
