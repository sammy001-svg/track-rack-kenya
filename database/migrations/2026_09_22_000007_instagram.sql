-- Link Tack Rack's Instagram, confirmed by the shop as its own account:
-- https://www.instagram.com/tackrackltd/
--
-- The footer already shows an Instagram icon whenever this setting has a value,
-- and it is added to the structured data (schema.org sameAs) Google reads, next
-- to the Facebook page.
--
-- Only fills the setting if it is still empty, so a URL entered in
-- Admin -> Settings is not overwritten. Safe to run more than once.
--
-- Sharon Ashley's X (@tackrack) and LinkedIn were found too, but they are her
-- personal accounts and the shop chose not to link them.

SET NAMES utf8mb4;

UPDATE `settings`
   SET `value` = 'https://www.instagram.com/tackrackltd/'
 WHERE `key_name` = 'social_instagram'
   AND (`value` IS NULL OR `value` = '');
