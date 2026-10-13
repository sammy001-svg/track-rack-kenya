-- Corrections from the review meeting of 8 October 2026.
--
-- 1. "Catalog" becomes "Catalogue" throughout, including in page copy.
--
-- 2. Saddle fitting is not done at the shop. Three pieces of copy offered it
--    there — the how-to-order page, the saddle fitting service and the fee
--    note under the booking form — and the booking form itself was worded as
--    though bringing the horse in were the default. Fittings happen at the
--    yard, so all four now say so.
--
-- Each statement matches the old wording exactly, so anything rewritten in the
-- admin since is left alone, and running this twice changes nothing.

SET NAMES utf8mb4;

-- 1. Spelling
UPDATE `pages`
   SET `subtitle` = REPLACE(`subtitle`, 'From catalog to yard', 'From catalogue to yard'),
       `body`     = REPLACE(`body`, 'Browse the catalog.', 'Browse the catalogue.')
 WHERE `slug` = 'how-to-order';

-- 2. Fitting happens at the yard, not at the shop
UPDATE `pages`
   SET `body` = REPLACE(
         `body`,
         'we will arrange a fitting &mdash; at the shop or at your yard',
         'we will arrange a fitting at your yard'
       )
 WHERE `slug` = 'how-to-order';

UPDATE `services`
   SET `description` = REPLACE(
         `description`,
         'every saddle we supply is fitted on the horse — at the shop or at your yard',
         'every saddle we supply is fitted on the horse, at your yard'
       )
 WHERE `slug` = 'saddle-fitting';

UPDATE `settings`
   SET `value` = 'Fitting is included with a saddle purchase. The yard visit is charged by distance, confirmed before we travel.'
 WHERE `key_name` = 'fitting_fee_note'
   AND `value` LIKE 'Fitting at the shop is free%';
