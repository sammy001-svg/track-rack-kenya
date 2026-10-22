-- =====================================================================
--  Sizes and colours for headcollars and lead ropes
--
--  The review set the nylon headcollar to Pony, Cob and Full in red, navy,
--  black, green, sky blue and purple. Here those are on the listing; on the
--  live site the headcollar offers no size or colour at all, because the
--  variants were only ever written by the catalogue import and that has
--  never run there.
--
--  Applied to both slugs the listing goes by — nylon-headcollar-colour-range
--  here, nylon-headcollar-assorted-colours on the live site — so it lands
--  wherever it is run. The same for the lead ropes.
--
--  Idempotent: each label is cleared for that listing before it is written,
--  so a second run leaves exactly the same rows.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  Nylon headcollar: sizes.
-- ---------------------------------------------------------------------
DELETE v FROM `product_variants` v
  JOIN `products` p ON p.`id` = v.`product_id`
 WHERE p.`slug` IN ('nylon-headcollar-colour-range', 'nylon-headcollar-assorted-colours')
   AND v.`label` = 'Size';

INSERT INTO `product_variants` (`product_id`, `label`, `value`, `sort_order`)
SELECT p.`id`, 'Size', s.`value`, s.`pos` FROM `products` p
  JOIN (SELECT 'Pony' AS `value`, 0 AS `pos`
        UNION ALL SELECT 'Cob', 1
        UNION ALL SELECT 'Full', 2) s
 WHERE p.`slug` IN ('nylon-headcollar-colour-range', 'nylon-headcollar-assorted-colours');

-- ---------------------------------------------------------------------
--  Nylon headcollar: colours, in the order the review listed them.
-- ---------------------------------------------------------------------
DELETE v FROM `product_variants` v
  JOIN `products` p ON p.`id` = v.`product_id`
 WHERE p.`slug` IN ('nylon-headcollar-colour-range', 'nylon-headcollar-assorted-colours')
   AND v.`label` = 'Colour';

INSERT INTO `product_variants` (`product_id`, `label`, `value`, `sort_order`)
SELECT p.`id`, 'Colour', c.`value`, c.`pos` FROM `products` p
  JOIN (SELECT 'Red' AS `value`, 0 AS `pos`
        UNION ALL SELECT 'Navy', 1
        UNION ALL SELECT 'Black', 2
        UNION ALL SELECT 'Green', 3
        UNION ALL SELECT 'Sky Blue', 4
        UNION ALL SELECT 'Purple', 5) c
 WHERE p.`slug` IN ('nylon-headcollar-colour-range', 'nylon-headcollar-assorted-colours');

-- ---------------------------------------------------------------------
--  Lead ropes: the colours we already stock, pending the client's list.
-- ---------------------------------------------------------------------
DELETE v FROM `product_variants` v
  JOIN `products` p ON p.`id` = v.`product_id`
 WHERE p.`slug` IN ('rope-lead-ropes', 'rope-lead-ropes-various-colours-3m')
   AND v.`label` IN ('Colour', 'Length');

INSERT INTO `product_variants` (`product_id`, `label`, `value`, `sort_order`)
SELECT p.`id`, 'Length', '3m', 0 FROM `products` p
 WHERE p.`slug` IN ('rope-lead-ropes', 'rope-lead-ropes-various-colours-3m');

INSERT INTO `product_variants` (`product_id`, `label`, `value`, `sort_order`)
SELECT p.`id`, 'Colour', c.`value`, c.`pos` FROM `products` p
  JOIN (SELECT 'Red' AS `value`, 0 AS `pos`
        UNION ALL SELECT 'Green', 1
        UNION ALL SELECT 'Black', 2
        UNION ALL SELECT 'Purple', 3
        UNION ALL SELECT 'Sky Blue', 4) c
 WHERE p.`slug` IN ('rope-lead-ropes', 'rope-lead-ropes-various-colours-3m');
