-- =====================================================================
--  Put every Girths listing on a shelf
--
--  Girths has its five shelves — Dressage, Anti-Chafe, Fleece-Lined,
--  Borghey and Elastic — but on the live site the Borghey girth sits loose
--  on the section, because it is listed there as 'black-burghley-girth'
--  and we carry it as 'borghey-girth'. Moving it by slug missed it.
--
--  Match on what a listing is instead, accepting either spelling. Only
--  listings sitting directly on the section are touched, so anything
--  already shelved stays put and a second run changes nothing.
-- =====================================================================

SET NAMES utf8mb4;

UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'girths'
  JOIN `categories` dst ON dst.`slug` = 'dressage-girths'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'dressage';

UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'girths'
  JOIN `categories` dst ON dst.`slug` = 'girths-anti-chafe'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'anti.?chafe';

UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'girths'
  JOIN `categories` dst ON dst.`slug` = 'girths-fleece-lined'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'fleece';

-- Both spellings: the live site writes it Burghley, we write it Borghey.
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'girths'
  JOIN `categories` dst ON dst.`slug` = 'girths-borghey'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'borghey|burghley';

UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'girths'
  JOIN `categories` dst ON dst.`slug` = 'girths-elastic'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'elastic';
