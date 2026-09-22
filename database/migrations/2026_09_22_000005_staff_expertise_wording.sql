-- Tack Rack's staff are experts in the products; they are not riders. Two
-- category descriptions — the text Google shows under the link — said
-- otherwise. This replaces both.
--
-- Each UPDATE matches the old wording exactly, so a description someone has
-- since rewritten in Admin -> Categories is left alone, and running this file
-- twice changes nothing the second time.
--
-- The same claim was removed from the home page in the same change; that text
-- lives in app/Views/site/home.php, not the database.

SET NAMES utf8mb4;

UPDATE `categories`
   SET `meta_desc` = 'Riding boots, breeches, jackets, gloves and helmets for every discipline. Rider apparel from Tack Rack, Nairobi, with expert advice on fit.'
 WHERE `slug` = 'rider'
   AND `meta_desc` = 'Riding boots, breeches, jackets, gloves and helmets for every discipline. Rider apparel from Tack Rack, Nairobi — fitted advice from staff who ride.';

UPDATE `categories`
   SET `meta_desc` = 'Electrolytes, hoof and joint supplements and first aid for the tack room, with expert product advice from Tack Rack, Ngong Road, Nairobi.'
 WHERE `slug` = 'horse-health-supplements'
   AND `meta_desc` = 'Electrolytes, hoof and joint supplements and first aid for the tack room. Advice from staff who keep horses in Kenya themselves.';
