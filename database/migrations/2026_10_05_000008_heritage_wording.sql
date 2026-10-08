-- Two corrections to page copy.
--
-- 1. The Heritage page still said "Our staff ride". The staff are experts in
--    the products, not riders; the same claim was removed from the home page
--    and two category descriptions in migration 000005, but this phrasing was
--    missed there.
--
-- 2. "Rider, Horse and Stable" predates the catalogue being regrouped to the
--    shop's own departments, where the third is the Yard.
--
-- Both match the old wording exactly, so anything rewritten in the admin since
-- is left alone, and running this twice changes nothing.

SET NAMES utf8mb4;

UPDATE `pages`
   SET `body` = REPLACE(
         `body`,
         'Our staff ride, and they will tell you plainly what a horse actually needs',
         'Our staff know the kit inside out, and will tell you plainly what a horse actually needs'
       )
 WHERE `slug` = 'heritage';

UPDATE `pages`
   SET `body` = REPLACE(`body`, 'Rider, Horse and Stable', 'Rider, Horse and Yard')
 WHERE `body` LIKE '%Rider, Horse and Stable%';
