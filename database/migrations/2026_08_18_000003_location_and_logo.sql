-- =====================================================================
--  Real location details, taken from the Tack Rack Ltd Google Business
--  Profile, plus the logo as the default share image.
--
--  These overwrite the placeholders seeded at build time, so this file uses
--  ON DUPLICATE KEY UPDATE rather than IGNORE. Anything you have since
--  corrected by hand in Admin -> Settings will be replaced by these values;
--  edit this file first if you would rather keep your own wording.
-- =====================================================================

SET NAMES utf8mb4;

INSERT INTO `settings` (`key_name`,`value`,`group_name`,`label`,`input_type`,`sort_order`) VALUES
  ('contact_address',
   'MacNaughton Centre, Off Ngong Road',
   'contact','Street address','text',4),

  ('contact_directions',
   'Near the Chequered Flag, opposite St Christopher''s School',
   'contact','How to find us (shown under the address)','text',5),

  ('contact_postal',
   'P.O. Box 57, Karen 00502, Nairobi, Kenya',
   'contact','Postal address','text',6),

  ('contact_phone','+254 722 763 279','contact','Primary phone','tel',1),

  -- Google Business Profile hours: weekdays close at 5pm, Saturday opens 8:30am.
  ('contact_hours',
   'Monday - Friday 8:30am - 5:00pm | Saturday 8:30am - 1:00pm',
   'contact','Opening hours','text',7),
  ('hours_weekday_open','08:30','contact','Weekday opening time (24h)','text',11),
  ('hours_weekday_close','17:00','contact','Weekday closing time (24h)','text',12),
  ('hours_saturday_open','08:30','contact','Saturday opening time (24h, blank if closed)','text',13),
  ('hours_saturday_close','13:00','contact','Saturday closing time (24h, blank if closed)','text',14),

  -- Derived from the two landmarks in the listing (The Chequered Flag and
  -- St Christopher's School) on Ngong Road. Accurate to roughly 100m — replace
  -- with the exact pin from Google Maps when convenient.
  ('geo_latitude','-1.318960','contact','Map latitude (from Google Maps)','text',9),
  ('geo_longitude','36.715030','contact','Map longitude (from Google Maps)','text',10),

  -- Resolves to the live Google listing rather than a fixed coordinate, so the
  -- map always shows whatever is current on the business profile.
  ('map_embed',
   'https://www.google.com/maps?q=Tack+Rack+Ltd,+Ngong+Road,+Nairobi,+Kenya&output=embed',
   'contact','Google Maps embed URL','url',8),

  ('map_link',
   'https://www.google.com/maps/search/?api=1&query=Tack+Rack+Ltd%2C+Ngong+Road%2C+Nairobi',
   'contact','Directions link','url',15),

  ('seo_share_image','/assets/img/og-logo.jpg','seo','Default share image (1200x630)','image',5)

ON DUPLICATE KEY UPDATE
  `value`      = VALUES(`value`),
  `group_name` = VALUES(`group_name`),
  `label`      = VALUES(`label`),
  `input_type` = VALUES(`input_type`),
  `sort_order` = VALUES(`sort_order`);
