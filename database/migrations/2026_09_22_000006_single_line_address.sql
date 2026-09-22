-- One address, worded exactly as it appears on Tack Rack's Google Business
-- Profile, everywhere on the site:
--
--   Off Ngong Road, Near Chequered Flag opposite St Christophers School, Nairobi
--
-- It replaces the two-part version ("MacNaughton Centre, Off Ngong Road" with
-- "Near the Chequered Flag, opposite St Christopher's School" underneath), so
-- the directions setting is emptied — every template that shows it already
-- skips it when blank.
--
-- Also replaces the MacNaughton Centre references in the Heritage page and in
-- fifteen product meta descriptions. Those statements match the old wording
-- only, so anything rewritten in the admin since is left alone. Safe to run
-- more than once.

SET NAMES utf8mb4;

UPDATE `settings`
   SET `value` = 'Off Ngong Road, Near Chequered Flag opposite St Christophers School, Nairobi'
 WHERE `key_name` = 'contact_address';

UPDATE `settings`
   SET `value` = ''
 WHERE `key_name` = 'contact_directions';

UPDATE `pages`
   SET `body` = REPLACE(`body`,
       'We are based at the MacNaughton Business Centre off Ngong Road in Nairobi',
       'We are based off Ngong Road, near the Chequered Flag opposite St Christophers School, Nairobi')
 WHERE `slug` = 'heritage';

UPDATE `products`
   SET `meta_desc` = REPLACE(`meta_desc`, 'Tack Rack, MacNaughton Centre, Ngong Road, Nairobi', 'Tack Rack, Ngong Road, Nairobi')
 WHERE `meta_desc` LIKE '%MacNaughton Centre%';

UPDATE `products`
   SET `meta_desc` = REPLACE(`meta_desc`, 'From our shop at the MacNaughton Centre, Ngong Road.', 'From our shop off Ngong Road, Nairobi.')
 WHERE `meta_desc` LIKE '%MacNaughton Centre%';
