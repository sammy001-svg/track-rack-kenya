-- Tack Rack — sizes, colours and packs
-- Generated 2026-10-17 15:46 by bin/export-variants-sql.php. Do not edit by hand.
--
-- 407 options across 85 products, from 36 rows of the
-- client's sizes & colours sheet (database/variants.php).
--
-- Import through phpMyAdmin: select the site database, open the Import tab,
-- choose this file, and run it. It is safe to run on a live site.
--
-- This file is additive. It writes to `product_variants` and to nothing else:
-- no product, category, image, order, quote, booking or setting is created,
-- changed or removed by it. Products are matched on their slug, and the slug
-- list covers both this catalogue and the live site's, which names a number of
-- things differently. A product that is not present is skipped rather than
-- guessed at, and running the file a second time replaces its own rows instead
-- of duplicating them.

SET NAMES utf8mb4;

START TRANSACTION;

-- Clear only the rows this file manages, so a second run replaces them.
DELETE v FROM `product_variants` v
  JOIN `products` p ON p.`id` = v.`product_id`
 WHERE p.`slug` IN (
  'aluminium-horseshoes',
  'anatomic-brushing-boots',
  'beige-riding-breeches',
  'black-burghley-girth',
  'black-elastic-girth',
  'black-full-seat-breeches',
  'black-half-chaps',
  'black-leather-general-purpose-saddle',
  'black-leather-snaffle-bridle',
  'black-or-havana-leather-flash-bridle',
  'black-skull-cap',
  'borghey-girth',
  'brown-leather-gaiters',
  'brushing-boots',
  'brushing-boots-2',
  'buzz-off-fly-repellent',
  'champion-black-skull-cap',
  'compositi-lightweight-stirrups',
  'dressage-square-black',
  'dressage-square-white',
  'dubbin',
  'endeavon-lemon-shampoo',
  'equimins-fly-midge-repellent',
  'fleece-lined-girth',
  'fleece-polo-stable-bandages',
  'flyguard-pro-fine-mesh-fly-mask',
  'full-seat-breeches',
  'glycerine-bar-leather-saddle-soap',
  'glycerine-leather-saddle-soap',
  'gold-label-mane-coat-tail-lotion',
  'half-chaps',
  'havana-leather-snaffle-bridle',
  'hkm-velvet-riding-hat',
  'hoof-oil',
  'hy-leather-jodhpur-boots-black',
  'hy-leather-jodhpur-boots-brown',
  'hy-leather-jodhpur-boots-brown-black',
  'jeffries-elite-leather-saddle',
  'jodhpur-boots-black-brown',
  'korsteel-stainless-steel-stirrup-irons',
  'leather-bib-martingale',
  'leather-dressing',
  'leather-martingale',
  'leather-saddle-soap',
  'leather-soap',
  'lemon-shampoo',
  'loose-ring-jointed-snaffle',
  'loose-ring-snaffle-with-copper-lozenge',
  'matt-black-vented-riding-helmet',
  'natural-clay',
  'navy-riding-breeches',
  'neoprene-overreach-boots',
  'night-rug',
  'night-rugs',
  'nylon-headcollar-assorted-colours',
  'nylon-headcollar-colour-range',
  'optima-horseshoe-nails',
  'overreach-boots-red',
  'padded-anti-chafe-girth',
  'peacock-safety-stirrup-irons',
  'quilted-gp-numnah',
  'quilted-gp-numnah-2',
  'quilted-gp-numnah-black',
  'quilted-gp-numnah-navy',
  'quilted-gp-numnah-red',
  'quilted-gp-numnah-teal',
  'quilted-gp-numnah-various-colours',
  'riding-breeches',
  'rope-lead-ropes',
  'rope-lead-ropes-various-colours-3m',
  'rubber-overreach-boots',
  'rubber-pull-on-overreach-boots-or-velcro-closure',
  'shires-loose-ring-snaffle-with-copper-lozenge',
  'shires-stainless-steel-snaffle-bit',
  'short-dressage-girth',
  'stainless-steel-stirrup-irons',
  'steel-horseshoes',
  'stockholm-tar',
  'thorowgood-leather-saddle',
  'thorowgood-saddle',
  'training-bit-with-rope-cheeks',
  'velvet-riding-hat',
  'vetsbrands-summer-spray',
  'wintec-2000-all-purpose-saddle',
  'wintec-synthetic-saddle'
);

-- The options themselves, joined onto whatever id each product has here.
INSERT INTO `product_variants` (`product_id`, `label`, `value`, `sort_order`)
SELECT p.`id`, v.`label`, v.`value`, v.`sort_order`
  FROM `products` p
  JOIN (
    SELECT 'black-skull-cap' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-skull-cap' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-skull-cap' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'champion-black-skull-cap' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'champion-black-skull-cap' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'champion-black-skull-cap' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'hkm-velvet-riding-hat' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'hkm-velvet-riding-hat' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'hkm-velvet-riding-hat' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'matt-black-vented-riding-helmet' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'matt-black-vented-riding-helmet' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'matt-black-vented-riding-helmet' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'velvet-riding-hat' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'velvet-riding-hat' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'velvet-riding-hat' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'loose-ring-jointed-snaffle' AS `slug`, 'Size' AS `label`, '4.5\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'loose-ring-jointed-snaffle' AS `slug`, 'Size' AS `label`, '5\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'loose-ring-jointed-snaffle' AS `slug`, 'Size' AS `label`, '5.5\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'loose-ring-snaffle-with-copper-lozenge' AS `slug`, 'Size' AS `label`, '4.5\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'loose-ring-snaffle-with-copper-lozenge' AS `slug`, 'Size' AS `label`, '5\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'loose-ring-snaffle-with-copper-lozenge' AS `slug`, 'Size' AS `label`, '5.5\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'shires-loose-ring-snaffle-with-copper-lozenge' AS `slug`, 'Size' AS `label`, '4.5\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'shires-loose-ring-snaffle-with-copper-lozenge' AS `slug`, 'Size' AS `label`, '5\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'shires-loose-ring-snaffle-with-copper-lozenge' AS `slug`, 'Size' AS `label`, '5.5\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'shires-stainless-steel-snaffle-bit' AS `slug`, 'Size' AS `label`, '4.5\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'shires-stainless-steel-snaffle-bit' AS `slug`, 'Size' AS `label`, '5\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'shires-stainless-steel-snaffle-bit' AS `slug`, 'Size' AS `label`, '5.5\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'training-bit-with-rope-cheeks' AS `slug`, 'Size' AS `label`, '4.5\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'training-bit-with-rope-cheeks' AS `slug`, 'Size' AS `label`, '5\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'training-bit-with-rope-cheeks' AS `slug`, 'Size' AS `label`, '5.5\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'compositi-lightweight-stirrups' AS `slug`, 'Size' AS `label`, '4\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'compositi-lightweight-stirrups' AS `slug`, 'Size' AS `label`, '4 1/4\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'compositi-lightweight-stirrups' AS `slug`, 'Size' AS `label`, '4 1/2\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'korsteel-stainless-steel-stirrup-irons' AS `slug`, 'Size' AS `label`, '4\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'korsteel-stainless-steel-stirrup-irons' AS `slug`, 'Size' AS `label`, '4 1/4\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'korsteel-stainless-steel-stirrup-irons' AS `slug`, 'Size' AS `label`, '4 1/2\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'peacock-safety-stirrup-irons' AS `slug`, 'Size' AS `label`, '4\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'peacock-safety-stirrup-irons' AS `slug`, 'Size' AS `label`, '4 1/4\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'peacock-safety-stirrup-irons' AS `slug`, 'Size' AS `label`, '4 1/2\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'stainless-steel-stirrup-irons' AS `slug`, 'Size' AS `label`, '4\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'stainless-steel-stirrup-irons' AS `slug`, 'Size' AS `label`, '4 1/4\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'stainless-steel-stirrup-irons' AS `slug`, 'Size' AS `label`, '4 1/2\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Size' AS `label`, '42\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Size' AS `label`, '44\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Size' AS `label`, '46\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Size' AS `label`, '48\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Size' AS `label`, '50\"' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'black-elastic-girth' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Size' AS `label`, '42\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Size' AS `label`, '44\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Size' AS `label`, '46\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Size' AS `label`, '48\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Size' AS `label`, '50\"' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'fleece-lined-girth' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Size' AS `label`, '42\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Size' AS `label`, '44\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Size' AS `label`, '46\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Size' AS `label`, '48\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Size' AS `label`, '50\"' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'padded-anti-chafe-girth' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Size' AS `label`, '22\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Size' AS `label`, '24\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Size' AS `label`, '26\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Size' AS `label`, '28\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Size' AS `label`, '30\"' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'short-dressage-girth' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'dressage-square-black' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'dressage-square-black' AS `slug`, 'Colour' AS `label`, 'White' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'dressage-square-black' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'dressage-square-black' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'dressage-square-black' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'dressage-square-white' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'dressage-square-white' AS `slug`, 'Colour' AS `label`, 'White' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'dressage-square-white' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'dressage-square-white' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'dressage-square-white' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-2' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-black' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-navy' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-red' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-teal' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Size' AS `label`, '15\" — 16 1/2\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Size' AS `label`, '17\" — 18\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'quilted-gp-numnah-various-colours' AS `slug`, 'Colour' AS `label`, 'Teal' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'anatomic-brushing-boots' AS `slug`, 'Colour' AS `label`, 'Grey' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'brushing-boots' AS `slug`, 'Colour' AS `label`, 'Grey' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'brushing-boots-2' AS `slug`, 'Colour' AS `label`, 'Grey' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'neoprene-overreach-boots' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'overreach-boots-red' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'rubber-overreach-boots' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'rubber-overreach-boots' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'rubber-overreach-boots' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'rubber-pull-on-overreach-boots-or-velcro-closure' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'rubber-pull-on-overreach-boots-or-velcro-closure' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'rubber-pull-on-overreach-boots-or-velcro-closure' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Size' AS `label`, 'One size' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Pack' AS `label`, 'x4 per packet' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Colour' AS `label`, 'Navy blue' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Colour' AS `label`, 'Grey' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Colour' AS `label`, 'Lime green' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Colour' AS `label`, 'White' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'fleece-polo-stable-bandages' AS `slug`, 'Colour' AS `label`, 'Royal blue' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'aluminium-horseshoes' AS `slug`, 'Size' AS `label`, '4' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'aluminium-horseshoes' AS `slug`, 'Size' AS `label`, '5' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'aluminium-horseshoes' AS `slug`, 'Size' AS `label`, '6' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'steel-horseshoes' AS `slug`, 'Size' AS `label`, '3' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'steel-horseshoes' AS `slug`, 'Size' AS `label`, '4' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'steel-horseshoes' AS `slug`, 'Size' AS `label`, '5' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'steel-horseshoes' AS `slug`, 'Size' AS `label`, '6' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'steel-horseshoes' AS `slug`, 'Size' AS `label`, '7' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'steel-horseshoes' AS `slug`, 'Size' AS `label`, '8' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Size' AS `label`, 'E3' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Size' AS `label`, 'E4' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Size' AS `label`, 'Crown Head I' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Size' AS `label`, 'ASV 1 3/4' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Size' AS `label`, 'FJ1' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Pack' AS `label`, 'Packet of 50' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'optima-horseshoe-nails' AS `slug`, 'Pack' AS `label`, 'Box of 250' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'dubbin' AS `slug`, 'Size' AS `label`, '250ml tub' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'dubbin' AS `slug`, 'Size' AS `label`, '500ml tub' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'glycerine-bar-leather-saddle-soap' AS `slug`, 'Size' AS `label`, 'Bar' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'glycerine-bar-leather-saddle-soap' AS `slug`, 'Size' AS `label`, '500ml tub' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'glycerine-leather-saddle-soap' AS `slug`, 'Size' AS `label`, 'Bar' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'glycerine-leather-saddle-soap' AS `slug`, 'Size' AS `label`, '500ml tub' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'leather-saddle-soap' AS `slug`, 'Size' AS `label`, 'Bar' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'leather-saddle-soap' AS `slug`, 'Size' AS `label`, '500ml tub' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'leather-soap' AS `slug`, 'Size' AS `label`, 'Bar' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'leather-soap' AS `slug`, 'Size' AS `label`, '500ml tub' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'leather-dressing' AS `slug`, 'Size' AS `label`, '500ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'leather-dressing' AS `slug`, 'Size' AS `label`, '1 litre' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'natural-clay' AS `slug`, 'Size' AS `label`, '2kg' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'natural-clay' AS `slug`, 'Size' AS `label`, '4kg' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'hoof-oil' AS `slug`, 'Size' AS `label`, '500ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'hoof-oil' AS `slug`, 'Size' AS `label`, '1 litre' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'stockholm-tar' AS `slug`, 'Size' AS `label`, '500ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'stockholm-tar' AS `slug`, 'Size' AS `label`, '1 litre' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'vetsbrands-summer-spray' AS `slug`, 'Size' AS `label`, '1 litre' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'vetsbrands-summer-spray' AS `slug`, 'Size' AS `label`, '5 litres' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'buzz-off-fly-repellent' AS `slug`, 'Size' AS `label`, '750ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'buzz-off-fly-repellent' AS `slug`, 'Size' AS `label`, '5 litres' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'equimins-fly-midge-repellent' AS `slug`, 'Size' AS `label`, '750ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'equimins-fly-midge-repellent' AS `slug`, 'Size' AS `label`, '5 litres' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'endeavon-lemon-shampoo' AS `slug`, 'Size' AS `label`, '500ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'endeavon-lemon-shampoo' AS `slug`, 'Size' AS `label`, '5 litres' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'lemon-shampoo' AS `slug`, 'Size' AS `label`, '500ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'lemon-shampoo' AS `slug`, 'Size' AS `label`, '5 litres' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'gold-label-mane-coat-tail-lotion' AS `slug`, 'Size' AS `label`, '500ml' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'gold-label-mane-coat-tail-lotion' AS `slug`, 'Size' AS `label`, '2.5 litres' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'gold-label-mane-coat-tail-lotion' AS `slug`, 'Size' AS `label`, '5 litres' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Colour' AS `label`, 'Navy blue' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 7 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-assorted-colours' AS `slug`, 'Colour' AS `label`, 'Purple' AS `value`, 8 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Colour' AS `label`, 'Blue' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Colour' AS `label`, 'Navy blue' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 7 AS `sort_order`
    UNION ALL SELECT 'nylon-headcollar-colour-range' AS `slug`, 'Colour' AS `label`, 'Purple' AS `value`, 8 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes' AS `slug`, 'Length' AS `label`, '3m' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes' AS `slug`, 'Colour' AS `label`, 'Purple' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes' AS `slug`, 'Colour' AS `label`, 'Sky blue' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes-various-colours-3m' AS `slug`, 'Length' AS `label`, '3m' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes-various-colours-3m' AS `slug`, 'Colour' AS `label`, 'Red' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes-various-colours-3m' AS `slug`, 'Colour' AS `label`, 'Green' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes-various-colours-3m' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes-various-colours-3m' AS `slug`, 'Colour' AS `label`, 'Purple' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'rope-lead-ropes-various-colours-3m' AS `slug`, 'Colour' AS `label`, 'Sky blue' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'black-leather-snaffle-bridle' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-leather-snaffle-bridle' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-leather-snaffle-bridle' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-leather-snaffle-bridle' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-leather-snaffle-bridle' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'black-or-havana-leather-flash-bridle' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-or-havana-leather-flash-bridle' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-or-havana-leather-flash-bridle' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-or-havana-leather-flash-bridle' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-or-havana-leather-flash-bridle' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'havana-leather-snaffle-bridle' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'havana-leather-snaffle-bridle' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'havana-leather-snaffle-bridle' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'havana-leather-snaffle-bridle' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'havana-leather-snaffle-bridle' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'leather-bib-martingale' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'leather-bib-martingale' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'leather-bib-martingale' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'leather-bib-martingale' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'leather-bib-martingale' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'leather-martingale' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'leather-martingale' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'leather-martingale' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'leather-martingale' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'leather-martingale' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Child 12' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Child 13' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Child 1' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Child 2' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Child 3' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Adult 4' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Adult 5' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Adult 6' AS `value`, 7 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Adult 7' AS `value`, 8 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-black' AS `slug`, 'Size' AS `label`, 'Adult 8' AS `value`, 9 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Child 12' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Child 13' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Child 1' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Child 2' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Child 3' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Adult 4' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Adult 5' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Adult 6' AS `value`, 7 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Adult 7' AS `value`, 8 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown' AS `slug`, 'Size' AS `label`, 'Adult 8' AS `value`, 9 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Child 12' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Child 13' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Child 1' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Child 2' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Child 3' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Adult 4' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Adult 5' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Adult 6' AS `value`, 7 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Adult 7' AS `value`, 8 AS `sort_order`
    UNION ALL SELECT 'hy-leather-jodhpur-boots-brown-black' AS `slug`, 'Size' AS `label`, 'Adult 8' AS `value`, 9 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Child 12' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Child 13' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Child 1' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Child 2' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Child 3' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Adult 4' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Adult 5' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Adult 6' AS `value`, 7 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Adult 7' AS `value`, 8 AS `sort_order`
    UNION ALL SELECT 'jodhpur-boots-black-brown' AS `slug`, 'Size' AS `label`, 'Adult 8' AS `value`, 9 AS `sort_order`
    UNION ALL SELECT 'beige-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Children' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'beige-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Maids / Boys' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'beige-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Ladies' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'beige-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Gents' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Children' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Maids / Boys' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Ladies' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Gents' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Children' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Maids / Boys' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Ladies' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'full-seat-breeches' AS `slug`, 'Fit' AS `label`, 'Gents' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'navy-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Children' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'navy-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Maids / Boys' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'navy-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Ladies' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'navy-riding-breeches' AS `slug`, 'Fit' AS `label`, 'Gents' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'riding-breeches' AS `slug`, 'Fit' AS `label`, 'Children' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'riding-breeches' AS `slug`, 'Fit' AS `label`, 'Maids / Boys' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'riding-breeches' AS `slug`, 'Fit' AS `label`, 'Ladies' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'riding-breeches' AS `slug`, 'Fit' AS `label`, 'Gents' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-leather-general-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '16\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-leather-general-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '16 1/2\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-leather-general-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '17\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-leather-general-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '17 1/2\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'jeffries-elite-leather-saddle' AS `slug`, 'Seat size' AS `label`, '16\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'jeffries-elite-leather-saddle' AS `slug`, 'Seat size' AS `label`, '16 1/2\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'jeffries-elite-leather-saddle' AS `slug`, 'Seat size' AS `label`, '17\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'jeffries-elite-leather-saddle' AS `slug`, 'Seat size' AS `label`, '17 1/2\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'thorowgood-leather-saddle' AS `slug`, 'Seat size' AS `label`, '16\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'thorowgood-leather-saddle' AS `slug`, 'Seat size' AS `label`, '16 1/2\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'thorowgood-leather-saddle' AS `slug`, 'Seat size' AS `label`, '17\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'thorowgood-leather-saddle' AS `slug`, 'Seat size' AS `label`, '17 1/2\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'thorowgood-saddle' AS `slug`, 'Seat size' AS `label`, '16\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'thorowgood-saddle' AS `slug`, 'Seat size' AS `label`, '16 1/2\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'thorowgood-saddle' AS `slug`, 'Seat size' AS `label`, '17\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'thorowgood-saddle' AS `slug`, 'Seat size' AS `label`, '17 1/2\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'wintec-2000-all-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '16\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'wintec-2000-all-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '16 1/2\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'wintec-2000-all-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '17\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'wintec-2000-all-purpose-saddle' AS `slug`, 'Seat size' AS `label`, '17 1/2\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'wintec-synthetic-saddle' AS `slug`, 'Seat size' AS `label`, '16\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'wintec-synthetic-saddle' AS `slug`, 'Seat size' AS `label`, '16 1/2\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'wintec-synthetic-saddle' AS `slug`, 'Seat size' AS `label`, '17\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'wintec-synthetic-saddle' AS `slug`, 'Seat size' AS `label`, '17 1/2\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Size' AS `label`, 'XS' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Size' AS `label`, 'XL' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'black-half-chaps' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Size' AS `label`, 'XS' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Size' AS `label`, 'XL' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'brown-leather-gaiters' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Size' AS `label`, 'XS' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Size' AS `label`, 'Small' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Size' AS `label`, 'Medium' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Size' AS `label`, 'Large' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Size' AS `label`, 'XL' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Colour' AS `label`, 'Brown' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'half-chaps' AS `slug`, 'Colour' AS `label`, 'Black' AS `value`, 6 AS `sort_order`
    UNION ALL SELECT 'flyguard-pro-fine-mesh-fly-mask' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'flyguard-pro-fine-mesh-fly-mask' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'flyguard-pro-fine-mesh-fly-mask' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'flyguard-pro-fine-mesh-fly-mask' AS `slug`, 'Style' AS `label`, 'With ears' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'flyguard-pro-fine-mesh-fly-mask' AS `slug`, 'Style' AS `label`, 'Without ears' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'borghey-girth' AS `slug`, 'Size' AS `label`, '40\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'borghey-girth' AS `slug`, 'Size' AS `label`, '42\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'borghey-girth' AS `slug`, 'Size' AS `label`, '44\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'borghey-girth' AS `slug`, 'Size' AS `label`, '46\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'borghey-girth' AS `slug`, 'Size' AS `label`, '48\"' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'borghey-girth' AS `slug`, 'Size' AS `label`, '50\"' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'black-burghley-girth' AS `slug`, 'Size' AS `label`, '40\"' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'black-burghley-girth' AS `slug`, 'Size' AS `label`, '42\"' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'black-burghley-girth' AS `slug`, 'Size' AS `label`, '44\"' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'black-burghley-girth' AS `slug`, 'Size' AS `label`, '46\"' AS `value`, 3 AS `sort_order`
    UNION ALL SELECT 'black-burghley-girth' AS `slug`, 'Size' AS `label`, '48\"' AS `value`, 4 AS `sort_order`
    UNION ALL SELECT 'black-burghley-girth' AS `slug`, 'Size' AS `label`, '50\"' AS `value`, 5 AS `sort_order`
    UNION ALL SELECT 'night-rug' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'night-rug' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'night-rug' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
    UNION ALL SELECT 'night-rugs' AS `slug`, 'Size' AS `label`, 'Pony' AS `value`, 0 AS `sort_order`
    UNION ALL SELECT 'night-rugs' AS `slug`, 'Size' AS `label`, 'Cob' AS `value`, 1 AS `sort_order`
    UNION ALL SELECT 'night-rugs' AS `slug`, 'Size' AS `label`, 'Full' AS `value`, 2 AS `sort_order`
  ) v ON v.`slug` = p.`slug`;

COMMIT;
