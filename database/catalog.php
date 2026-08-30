<?php
/**
 * The real Tack Rack catalogue, built from the studio photography in
 * public/assets/img/Products.
 *
 * Every product below was identified by examining the photographs. Where a
 * maker's mark was legible it is recorded as the brand; where it was not, the
 * product is described by what it plainly is rather than guessing a brand.
 *
 * Each image carries its own caption, which becomes the alt text and the
 * gallery description on the product page.
 *
 * Consumed by bin/import-products.php.
 */

return [

// =====================================================================
//  SADDLES
// =====================================================================
[
    'name'     => 'Thorowgood Leather General Purpose Saddle',
    'category' => 'saddles-accessories',
    'brand'    => 'Thorowgood',
    'featured' => true,
    'short'    => 'A soft-leather general purpose saddle with a deep, comfortable seat.',
    'description' => "A general purpose saddle in soft black leather, cut for riders who school, jump and hack the same horse. The seat is deep enough to feel secure over a fence without locking the rider out of a flatwork position, and the panels are shaped to sit close to the horse.\n\nThorowgood saddles are known for being forgiving to fit and kind on the horse's back. As with every saddle we supply, this one is fitted on the horse before it leaves us.",
    'specs'    => "Leather: soft-grain black\nSeat: padded, general purpose cut\nPanels: shaped for close contact\nMaker: Thorowgood\nFitting: included, on the horse",
    'sizing'   => 'Seat size is taken from the rider, gullet and panel from the horse. Book a fitting before ordering — we travel to yards across Kenya.',
    'images'   => [
        ['1S3A5273.png', 'Full side profile of the saddle'],
        ['1S3A5276.png', 'Seat and cantle, showing the padded seam'],
        ['1S3A5277.png', 'Knee roll and panel from the front'],
        ['1S3A5278.png', 'Cantle and rear seat detail'],
        ['1S3A5281.png', "Thorowgood maker's label on the flap"],
        ['1S3A5284.png', 'Pommel, knee roll and stirrup bar'],
    ],
],
[
    'name'     => 'Thorowgood Leather Close Contact Saddle',
    'category' => 'saddles-accessories',
    'brand'    => 'Thorowgood',
    'featured' => true,
    'short'    => 'A close contact jumping saddle in black leather, forward cut.',
    'description' => "A close contact saddle with a forward-cut flap and a shallower seat, built to let the rider stay light and balanced over a course. The leather is soft from the outset rather than needing months of work to break in.\n\nFitted on the horse by our Society of Master Saddlers qualified fitter before delivery.",
    'specs'    => "Leather: soft-grain black\nFlap: forward cut for jumping\nSeat: shallow close contact\nMaker: Thorowgood\nFitting: included, on the horse",
    'sizing'   => 'Seat size from the rider, gullet and panel from the horse. Contact us to arrange a fitting.',
    'images'   => [
        ['1S3A5290.png', 'Full side profile of the saddle'],
        ['1S3A5292.png', 'Seat, cantle and flap together'],
        ['1S3A5293.png', 'Flap and stirrup bar detail'],
        ['1S3A5296.png', "Thorowgood maker's label"],
        ['1S3A5297.png', 'Cantle close-up'],
        ['1S3A5301.png', 'Side view showing the forward flap'],
    ],
],
[
    'name'     => 'Wintec Synthetic Saddle',
    'category' => 'saddles-accessories',
    'brand'    => 'Wintec',
    'short'    => 'A hard-wearing synthetic saddle with a grippy suede-effect seat.',
    'description' => "A synthetic saddle built for riders who want low maintenance without giving up fit. The suede-effect seat gives real grip, and the whole saddle can be washed down after a dusty ride rather than needing oiling.\n\nSynthetic saddles suit the Kenyan climate particularly well — they do not dry out and crack the way leather can at altitude.",
    'specs'    => "Material: synthetic with suede-effect seat\nCare: wipe or wash clean, no oiling\nMaker: Wintec\nFitting: included, on the horse",
    'sizing'   => 'Fitted on the horse. Wintec gullets are adjustable, which makes them a sensible choice for a young or changing horse.',
    'images'   => [
        ['1S3A5306.png', 'Seat and flap, showing the suede-effect surface'],
        ['1S3A5308.png', 'Wintec badge on the seat'],
    ],
],
[
    'name'     => 'Jeffries Elite Leather Saddle',
    'category' => 'saddles-accessories',
    'brand'    => 'Jeffries',
    'featured' => true,
    'short'    => 'A havana brown English saddle, hand finished, with the Elite embossing.',
    'description' => "A traditional English saddle in rich havana brown leather from Jeffries, one of the long-standing Walsall saddlery names. The Elite model is hand finished, with a warm, deep colour that comes up beautifully with regular feeding.\n\nA saddle of this quality is a long-term purchase. It is worth having it fitted properly and re-checked as the horse changes shape.",
    'specs'    => "Leather: havana brown, hand finished\nModel: Elite, embossed on the flap\nMaker: Jeffries Saddlery, England\nFittings: stainless steel\nFitting: included, on the horse",
    'sizing'   => 'Seat size from the rider, gullet and panel from the horse. We recommend a fitting before purchase and an annual re-check.',
    'images'   => [
        ['1S3A5315.png', 'Seat and cantle in havana brown leather'],
        ['1S3A5316.png', 'ELITE embossing and hand hole on the flap'],
        ['1S3A5317.png', "Jeffries maker's badge on the cantle"],
        ['1S3A5322.png', 'Flap detail showing the Elite embossing'],
    ],
],
[
    'name'     => 'Black Leather General Purpose Saddle',
    'category' => 'saddles-accessories',
    'short'    => 'A classic black leather GP saddle with a padded seat.',
    'description' => "A straightforward black leather general purpose saddle — the saddle most riders in Kenya end up using day to day. Comfortable enough for a long hack, secure enough for a lesson and a small course.\n\nFitted on the horse before it leaves the shop.",
    'specs'    => "Leather: black\nSeat: padded general purpose\nFittings: stainless steel\nFitting: included, on the horse",
    'sizing'   => 'Seat size from the rider, gullet and panel from the horse.',
    'images'   => [
        ['1S3A5325.png', 'Full side profile of the saddle'],
        ['1S3A5326.png', 'Seat, flap and stirrup bar'],
        ['1S3A5327.png', 'Panel and hand hole detail'],
        ['1S3A5328.png', 'Cantle and seat seam'],
    ],
],
[
    'name'     => 'Black Leather Jump Saddle',
    'category' => 'saddles-accessories',
    'short'    => 'A forward-cut jumping saddle in smooth black leather.',
    'description' => "A jumping saddle with a forward-cut flap and a light, shallow seat that keeps the rider off the horse's back over a fence. Smooth black leather throughout.\n\nEvery saddle we supply is fitted on the horse before delivery.",
    'specs'    => "Leather: smooth black\nFlap: forward cut\nSeat: shallow, close contact\nFitting: included, on the horse",
    'sizing'   => 'Seat size from the rider, gullet and panel from the horse.',
    'images'   => [
        ['1S3A5336.png', 'Full side profile of the saddle'],
        ['1S3A5337.png', 'Seat and cantle detail'],
        ['1S3A5341.png', 'Pommel and knee roll from the front'],
    ],
],
[
    'name'     => 'Black Leather Dressage Saddle',
    'category' => 'saddles-accessories',
    'short'    => 'A straight-cut dressage saddle with a deep seat.',
    'description' => "A dressage saddle with a straight-cut flap and a deep seat, putting the rider in a long, upright position with the leg underneath them.\n\nFitted on the horse before delivery, and re-flocked in our own workshop as the horse changes shape.",
    'specs'    => "Leather: black\nFlap: straight cut for dressage\nSeat: deep\nFitting: included, on the horse",
    'sizing'   => 'Seat size from the rider, gullet and panel from the horse.',
    'images'   => [
        ['1S3A5342.png', "Maker's badge on the saddle flap"],
        ['1S3A5345.png', 'Full side profile of the saddle'],
        ['1S3A5349.png', 'Seat and cantle detail'],
    ],
],
[
    'name'     => 'Wintec 2000 All Purpose Saddle',
    'category' => 'saddles-accessories',
    'brand'    => 'Wintec',
    'featured' => true,
    'short'    => 'The Wintec 2000 — adjustable, washable and genuinely low maintenance.',
    'description' => "The Wintec 2000 is an all purpose synthetic saddle with an adjustable gullet, so the same saddle can be re-fitted as a horse changes shape or moved between horses of similar build. It washes clean, does not dry out, and weighs noticeably less than a leather equivalent.\n\nA sensible first saddle, and a practical choice for a busy yard.",
    'specs'    => "Material: synthetic, washable\nGullet: adjustable (EASY-CHANGE system)\nSeat: all purpose\nMaker: Wintec\nFitting: included, on the horse",
    'sizing'   => 'The adjustable gullet is changed to suit the horse at fitting. Seat size is taken from the rider.',
    'images'   => [
        ['1S3A5354.png', 'Full side profile of the Wintec 2000'],
        ['1S3A5356.png', 'Seat and cantle, showing the suede-effect grip'],
        ['1S3A5358.png', 'Knee roll and flap detail'],
        ['1S3A5361.png', 'Seat and pommel from above'],
        ['1S3A5363.png', 'Wintec 2000 label and girth billet straps'],
    ],
],

// =====================================================================
//  HELMETS & HEAD PROTECTION
// =====================================================================
[
    'name'     => 'Black Skull Cap',
    'category' => 'helmets-head-protection',
    'short'    => 'A plain black skull cap, the standard for cross country and racing.',
    'description' => "A classic skull cap shape with no fixed peak, so a silk can be fitted over it. This is the hat of choice for cross country, racing work and anyone who wants maximum coverage at the back of the head.\n\nAlways buy a hat that has been fitted to your head. A hat that moves is a hat that will not do its job.",
    'specs'    => "Shell: hard, textured finish\nShape: skull cap, no fixed peak\nSilk: fits a standard hat silk (sold separately)",
    'sizing'   => 'Measure around the head just above the ears. Come in and be fitted if you possibly can — head shapes differ as much as sizes.',
    'images'   => [
        ['1S3A5368.png', 'Skull cap from the side'],
        ['1S3A5370.png', 'Close detail of the textured shell'],
    ],
],
[
    'name'     => 'HKM Velvet Riding Hat',
    'category' => 'helmets-head-protection',
    'brand'    => 'HKM',
    'short'    => 'A traditional black velvet riding hat with a fixed peak.',
    'description' => "The traditional show hat: black velvet finish with a fixed peak and a neat, low profile. Correct turnout for the show ring, Pony Club and everyday schooling.\n\nSupplied by HKM.",
    'specs'    => "Finish: black velvet\nPeak: fixed\nHarness: adjustable\nMaker: HKM",
    'sizing'   => 'Measure around the head just above the ears, and have it fitted before you ride in it.',
    'images'   => [
        ['1S3A5372.jpg', 'Velvet riding hat from the side'],
        ['1S3A5374.jpg', 'Close detail of the velvet finish and ribbing'],
        ['1S3A5375.jpg', 'Rear view with the HKM badge and harness'],
    ],
],
[
    'name'     => 'Matt Black Vented Riding Helmet',
    'category' => 'helmets-head-protection',
    'featured' => true,
    'short'    => 'A modern low-profile helmet with front venting, in matt black.',
    'description' => "A modern riding helmet with a low, clean profile and front vents that make a real difference schooling in the sun. The matt black finish is understated and does not show dust the way a gloss shell does.\n\nA good everyday helmet for schooling, hacking and lessons.",
    'specs'    => "Shell: matt black, low profile\nVentilation: front vents\nPeak: short moulded peak\nHarness: adjustable retention system",
    'sizing'   => 'Measure around the head just above the ears. We fit every helmet we sell — bring your head, not just a tape measure.',
    'images'   => [
        ['1S3A5380.jpg', 'Helmet from the side, showing the short peak'],
        ['1S3A5383.jpg', 'Front view with the vent detail'],
        ['1S3A5387.jpg', 'Close-up of the front vents'],
        ['1S3A5390.jpg', 'Rear view with the adjuster'],
    ],
],

// =====================================================================
//  BODY PROTECTORS
// =====================================================================
[
    'name'     => 'USG Body Protector — BETA 2018 Level 3',
    'category' => 'riding-jackets-vests',
    'brand'    => 'USG',
    'featured' => true,
    'short'    => 'Certified BETA 2018 Level 3 body protection, the standard for cross country.',
    'description' => "A body protector certified to BETA 2018 Level 3 — the level required for cross country and the level we would recommend to any rider jumping at speed. Segmented foam panels flex with the body so it does not fight you in the saddle.\n\nA body protector must fit closely to work. Come in and be measured; the fit matters far more than the brand.",
    'specs'    => "Certification: BETA 2018 Level 3\nConstruction: segmented foam panels\nAdjustment: shoulder, chest and waist\nMaker: USG",
    'sizing'   => 'Measured across the chest, waist and back length. A body protector that is too big is worse than none at all — always be fitted.',
    'images'   => [
        ['1S3A5396.jpg', 'Body protector from the front'],
        ['1S3A5397.jpg', 'Side view showing the adjustable straps'],
        ['1S3A5398.jpg', 'BETA 2018 Level 3 certification label'],
        ['1S3A5399.jpg', 'USG badge on the back panel'],
        ['1S3A5402.jpg', 'Shoulder and chest adjustment detail'],
    ],
],
[
    'name'     => 'Whitaker Body Protector',
    'category' => 'riding-jackets-vests',
    'brand'    => 'Whitaker',
    'short'    => 'A close-fitting body protector with a full-length front zip.',
    'description' => "A body protector from Whitaker with a full-length front zip and a mesh back panel for airflow. The zip makes it far easier to get in and out of than a pull-over design, particularly for younger riders.\n\nFitted at the shop — the protection only works if it sits correctly on the body.",
    'specs'    => "Closure: full-length front zip\nBack: ventilated mesh panel\nAdjustment: shoulder and waist straps\nMaker: Whitaker",
    'sizing'   => 'Measured across the chest, waist and back length. Please be fitted rather than ordering by guess.',
    'images'   => [
        ['1S3A5407.jpg', 'Body protector from the front, zip closed'],
        ['1S3A5408.jpg', 'Whitaker branding on the shoulder'],
        ['1S3A5409.jpg', 'Front zip and pull detail'],
    ],
],

// =====================================================================
//  BRIDLES & BITS
// =====================================================================
[
    'name'     => 'Black Leather Snaffle Bridle',
    'category' => 'bridles-bits-reins',
    'featured' => true,
    'short'    => 'A complete black leather snaffle bridle with reins.',
    'description' => "A full black leather snaffle bridle supplied with reins — headpiece, browband, cheekpieces, cavesson noseband and throatlash. Stainless steel fittings throughout.\n\nGood leather, kept fed, will outlast several cheaper bridles. In this climate a monthly balm is the difference between a bridle that lasts a decade and one that cracks in a season.",
    'specs'    => "Leather: black\nFittings: stainless steel\nIncludes: headpiece, browband, cheekpieces, noseband, throatlash and reins\nNoseband: cavesson",
    'sizing'   => 'Available in pony, cob and full. Cob fits the majority of horses in Kenya. Measure from the corner of the mouth over the poll and back.',
    'images'   => [
        ['1S3A5415.jpg', 'Complete bridle with reins, laid flat'],
        ['1S3A5416.jpg', 'Bridle shown from the other side'],
        ['1S3A5423.jpg', 'Browband, headpiece and buckle detail'],
        ['1S3A5425.jpg', 'Cheekpiece and noseband stitching'],
    ],
],
[
    'name'     => 'Havana Leather Snaffle Bridle',
    'category' => 'bridles-bits-reins',
    'short'    => 'The same bridle in warm havana brown leather.',
    'description' => "A complete snaffle bridle in havana brown leather with stainless steel fittings. Brown tack suits a bay or chestnut particularly well and is correct for hunting and most showing classes.\n\nSupplied with matching reins.",
    'specs'    => "Leather: havana brown\nFittings: stainless steel\nIncludes: headpiece, browband, cheekpieces, noseband, throatlash and reins\nNoseband: cavesson",
    'sizing'   => 'Available in pony, cob and full. Measure from the corner of the mouth over the poll and back.',
    'images'   => [
        ['1S3A5421.png', 'Complete havana bridle with reins'],
    ],
],
[
    'name'     => 'Shires Loose Ring Snaffle with Copper Lozenge',
    'category' => 'bridles-bits-reins',
    'brand'    => 'Shires',
    'short'    => 'A double-jointed loose ring snaffle with a copper lozenge centre.',
    'description' => "A loose ring snaffle with a double joint and a copper lozenge in the centre. The double joint removes the nutcracker action of a single-jointed bit, and the copper encourages the horse to salivate and soften.\n\nA fair, mild bit and a sensible first choice for a young horse.",
    'specs'    => "Material: stainless steel with copper lozenge\nMouthpiece: double jointed\nRings: loose ring\nMaker: Shires",
    'sizing'   => 'Measure the horse\'s mouth with a bit gauge, or bring the current bit in to compare. Sizes generally run 4.5in to 6in.',
    'images'   => [
        ['1S3A5604.png', 'Loose ring snaffle with the copper lozenge visible'],
    ],
],
[
    'name'     => 'Shires Stainless Steel Snaffle Bit',
    'category' => 'bridles-bits-reins',
    'brand'    => 'Shires',
    'short'    => 'A solid stainless steel snaffle from Shires.',
    'description' => "A well-made stainless steel snaffle from Shires. Solid stainless takes the wear, does not rust in a damp tack room and cleans up with nothing more than hot water.\n\nCome in and we will help you match the bit to the horse rather than to a catalogue.",
    'specs'    => "Material: solid stainless steel\nMaker: Shires\nFinish: polished",
    'sizing'   => 'Measure the horse\'s mouth with a bit gauge, or bring the current bit in to compare.',
    'images'   => [
        ['1S3A5608.png', 'Snaffle bit with the Shires tag attached'],
    ],
],
[
    'name'     => 'Loose Ring Jointed Snaffle',
    'category' => 'bridles-bits-reins',
    'short'    => 'A single-jointed loose ring snaffle in stainless steel.',
    'description' => "The most widely used bit there is: a single-jointed loose ring snaffle in stainless steel. Simple, mild in a quiet hand, and the right starting point for most horses.",
    'specs'    => "Material: stainless steel\nMouthpiece: single jointed\nRings: loose ring",
    'sizing'   => 'Measure the horse\'s mouth with a bit gauge, or bring the current bit in to compare.',
    'images'   => [
        ['1S3A5593.png', 'Loose ring jointed snaffle'],
    ],
],
[
    'name'     => 'Training Bit with Rope Cheeks',
    'category' => 'bridles-bits-reins',
    'short'    => 'A gag-action training bit with rope cheeks and a pulley.',
    'description' => "A training bit with rope cheeks running through the bit rings, giving a gag action that lifts the head rather than pulling back. Supplied with the cord, pulley and buckles as shown.\n\nA bit like this is a schooling tool, not a shortcut. Please talk to us about whether it is the right answer for your horse before buying.",
    'specs'    => "Material: stainless steel rings, corded cheeks\nAction: gag / poll lift\nIncludes: cord, pulley and adjusting buckles",
    'sizing'   => 'Measure the horse\'s mouth with a bit gauge. We would rather discuss the problem with you than sell you the wrong bit.',
    'images'   => [
        ['1S3A5599.png', 'Training bit showing the rope cheeks and pulley'],
    ],
],
[
    'name'     => 'Rubber Bit Guards',
    'category' => 'bridles-bits-reins',
    'short'    => 'Bit guards that stop the bit rubbing at the corners of the mouth.',
    'description' => "Round guards that sit between the bit ring and the horse's cheek, stopping the bit pinching or rubbing at the corners of the mouth. Particularly worth having on a loose ring bit or a young horse still settling into the contact.\n\nSold in pairs, in black and brown.",
    'specs'    => "Fitting: sold in pairs\nColours: black, brown\nUse: loose ring and eggbutt bits",
    'sizing'   => 'One size fits standard bit rings.',
    'images'   => [
        ['1S3A5609.png', 'Bit guards in black and brown'],
    ],
],

// =====================================================================
//  GIRTHS, STIRRUPS & SADDLE ACCESSORIES
// =====================================================================
[
    'name'     => 'Fleece Lined Girth',
    'category' => 'saddles-accessories',
    'short'    => 'A girth lined with soft fleece, for horses prone to girth galls.',
    'description' => "A girth with a soft fleece lining against the horse. The fleece spreads the pressure and stops the rubbing that causes girth galls, which makes it a sensible choice for a thin-skinned horse or one coming back into work.\n\nThe lining comes off and washes.",
    'specs'    => "Lining: soft fleece\nBuckles: stainless steel roller\nElastic: both ends\nCare: lining is washable",
    'sizing'   => 'Measure an existing girth from buckle to buckle, or measure the horse from four fingers below the saddle flap on one side to the same point on the other.',
    'images'   => [
        ['1S3A5620.png', 'Fleece lined girth, showing the soft lining'],
    ],
],
[
    'name'     => 'Black Elastic Girth',
    'category' => 'saddles-accessories',
    'short'    => 'A hard-wearing black girth with elastic at both ends.',
    'description' => "A straightforward black girth with elastic ends, which lets the horse's ribcage expand as it works rather than being held rigid. Stainless steel roller buckles make it easy to do up single-handed.",
    'specs'    => "Material: hard-wearing webbing\nElastic: both ends\nBuckles: stainless steel roller",
    'sizing'   => 'Measure an existing girth buckle to buckle. Sizes generally run 40in to 54in.',
    'images'   => [
        ['1S3A5623.png', 'Black elastic girth with the buckle detail'],
        ['1S3A5626.png', 'The same girth laid flat'],
    ],
],
[
    'name'     => 'Padded Anti-Chafe Girth',
    'category' => 'saddles-accessories',
    'featured' => true,
    'short'    => 'A shaped, padded girth designed to reduce rubbing behind the elbow.',
    'description' => "A shaped girth with a padded, textured lining that grips without chafing. The curve behind the elbow gives the horse room to move its foreleg freely, which matters a great deal to a horse that jumps.\n\nOne of the more worthwhile upgrades you can make to an otherwise ordinary set of tack.",
    'specs'    => "Shape: curved for elbow clearance\nLining: padded, anti-chafe texture\nBuckles: stainless steel roller\nElastic: both ends",
    'sizing'   => 'Measure an existing girth buckle to buckle.',
    'images'   => [
        ['1S3A5629.png', 'Padded girth, showing the shaped elbow cut-out'],
        ['1S3A5632.png', 'The lining and buckle detail'],
    ],
],
[
    'name'     => 'Short Dressage Girth',
    'category' => 'saddles-accessories',
    'short'    => 'A short girth for dressage saddles with long billets.',
    'description' => "A short, padded girth for use with a dressage saddle on long billet straps, keeping the buckles clear of the rider's leg. Neoprene lined so it grips without slipping.",
    'specs'    => "Type: short / dressage girth\nLining: padded neoprene\nBuckles: stainless steel roller",
    'sizing'   => 'Short girths generally run 20in to 34in. Measure an existing girth buckle to buckle.',
    'images'   => [
        ['1S3A5637.png', 'Short dressage girth from the side'],
    ],
],
[
    'name'     => 'Leather Girth Buckle Guards',
    'category' => 'saddles-accessories',
    'short'    => 'Leather guards that protect the saddle flap from girth buckles.',
    'description' => "Flat leather ovals that slide onto the girth billets and sit between the buckles and the saddle flap. Without them the buckles wear a permanent mark into the underside of the flap — a small item that quietly protects an expensive saddle.\n\nSold in pairs, in black and havana.",
    'specs'    => "Material: leather\nFitting: slides onto the girth billets\nColours: black, havana\nSold: in pairs",
    'sizing'   => 'One size fits standard billet straps.',
    'images'   => [
        ['1S3A5538.png', 'Buckle guards in black and havana, showing the billet slot'],
        ['1S3A5561.png', 'A padded pair in black and havana'],
        ['1S3A5563.png', 'Side view showing the thickness of the padded pair'],
    ],
],
[
    'name'     => 'Korsteel Stainless Steel Stirrup Irons',
    'category' => 'saddles-accessories',
    'brand'    => 'Korsteel',
    'featured' => true,
    'short'    => 'Solid stainless steel irons from Korsteel, with a wide tread.',
    'description' => "Solid stainless steel stirrup irons from Korsteel — heavy enough to hang correctly and find again if you lose one, and hard-wearing enough to last for years.\n\nAlways buy irons that leave roughly a centimetre either side of the boot. Too narrow and the foot jams; too wide and it can slide right through.",
    'specs'    => "Material: solid stainless steel\nTread: wide, with a rubber tread pad\nMaker: Korsteel",
    'sizing'   => 'Measured across the inside of the iron. Allow about 1cm clearance either side of your riding boot.',
    'images'   => [
        ['1S3A5567.png', 'Stainless steel iron showing the size stamp'],
        ['1S3A5570.png', 'The same iron from a second angle'],
    ],
],
[
    'name'     => 'Peacock Safety Stirrup Irons',
    'category' => 'saddles-accessories',
    'short'    => 'Safety irons with a rubber release band — the sensible choice for children.',
    'description' => "Peacock safety irons have a rubber band on the outside instead of a solid branch, so if a rider comes off the band releases and the foot comes free rather than being dragged.\n\nWe would put every child on these, and plenty of adults too.",
    'specs'    => "Material: stainless steel with rubber release band\nTread: white rubber tread pad\nUse: recommended for children and novice riders",
    'sizing'   => 'Measured across the inside of the iron. Allow about 1cm clearance either side of the boot.',
    'images'   => [
        ['1S3A5573.png', 'Peacock safety iron with the rubber release band'],
    ],
],
[
    'name'     => 'Compositi Lightweight Stirrups',
    'category' => 'saddles-accessories',
    'short'    => 'Light composite stirrups with a wide grippy tread, in several colours.',
    'description' => "Composite stirrups that weigh a fraction of a steel iron and will not rust. The wide tread is deeply grooved and grips a boot well, which makes a real difference to a rider who struggles to keep their foot still.\n\nAvailable in a range of tread colours.",
    'specs'    => "Material: reinforced composite\nTread: wide, deeply grooved\nWeight: considerably lighter than steel\nColours: several tread colours available",
    'sizing'   => 'Measured across the inside of the stirrup. Allow about 1cm clearance either side of the boot.',
    'images'   => [
        ['1S3A5578.png', 'Composite stirrup with a blue tread'],
        ['1S3A5583.png', 'The range shown in purple and blue'],
    ],
],
[
    'name'     => 'Rubber Stirrup Treads',
    'category' => 'saddles-accessories',
    'short'    => 'Replacement rubber treads to bring worn irons back to grip.',
    'description' => "Replacement rubber treads for stainless steel irons. A worn, polished tread is genuinely dangerous in the wet — replacing it costs very little and takes a minute.\n\nAvailable in black and white.",
    'specs'    => "Material: ribbed rubber\nColours: black, white\nSold: in pairs\nUse: standard stainless steel irons",
    'sizing'   => 'Match the tread to the width of your irons. Bring one in if you are unsure.',
    'images'   => [
        ['1S3A5586.png', 'White rubber treads'],
        ['1S3A5589.png', 'Black rubber treads'],
    ],
],
[
    'name'     => 'Leather Draw Reins',
    'category' => 'saddles-accessories',
    'short'    => 'Draw reins for schooling, with clips at both ends.',
    'description' => "Draw reins for schooling work, running from the girth through the bit rings back to the rider's hand. Used correctly and briefly they can help a horse find a rounder outline; used badly they teach it to lean.\n\nPlease speak to us, or to your instructor, before schooling in them.",
    'specs'    => "Material: leather with clip ends\nFitting: girth to bit ring to hand\nUse: schooling only, not for jumping",
    'sizing'   => 'One length, adjustable.',
    'images'   => [
        ['1S3A5542.png', 'Draw reins laid out with the clip ends visible'],
        ['1S3A5548.png', 'Detail of the clips and keeper'],
        ['1S3A5566.png', 'The reins coiled, showing the full length'],
    ],
],
[
    'name'     => 'Leather Bib Martingale',
    'category' => 'saddles-accessories',
    'short'    => 'A bib martingale in brown leather, safer than a standing pair.',
    'description' => "A bib martingale — a running martingale with a leather bib filling the space between the two straps, so the horse cannot get a foreleg caught through it. Standard for racing and cross country for exactly that reason.\n\nBrown leather with stainless fittings.",
    'specs'    => "Material: brown leather\nType: bib (running martingale with filled bib)\nFittings: stainless steel\nIncludes: neck strap",
    'sizing'   => 'Available in pony, cob and full. Adjust so that the rein runs in a straight line from bit to hand when the horse\'s head is at the correct height.',
    'images'   => [
        ['1S3A5551.png', 'Bib martingale laid flat, showing the leather bib'],
    ],
],

// =====================================================================
//  SADDLE PADS & NUMNAHS
// =====================================================================
[
    'name'     => 'Quilted GP Numnah — Red',
    'category' => 'saddle-pads-blankets',
    'short'    => 'A shaped, quilted numnah in red, cut for a GP saddle.',
    'description' => "A shaped numnah cut to follow the line of a general purpose saddle, quilted cotton with a soft lining and girth and billet straps. Washes clean and holds its colour.",
    'specs'    => "Shape: general purpose\nFace: quilted cotton\nAttachment: girth and billet straps\nCare: machine washable",
    'sizing'   => 'Available in cob and full. Full fits most horses of 15.2hh and over.',
    'images'   => [
        ['1S3A5513.png', 'Red GP numnah laid flat'],
        ['1S3A5529.png', 'Close detail of the quilting and binding'],
    ],
],
[
    'name'     => 'Quilted GP Numnah — Teal',
    'category' => 'saddle-pads-blankets',
    'short'    => 'The same shaped GP numnah in teal.',
    'description' => "A shaped GP numnah in teal, quilted cotton with a soft lining. A colour that looks well on a grey or a bay without being loud.",
    'specs'    => "Shape: general purpose\nFace: quilted cotton\nAttachment: girth and billet straps\nCare: machine washable",
    'sizing'   => 'Available in cob and full.',
    'images'   => [
        ['1S3A5515.png', 'Teal GP numnah laid flat'],
    ],
],
[
    'name'     => 'Quilted GP Numnah — Black',
    'category' => 'saddle-pads-blankets',
    'short'    => 'A shaped GP numnah in black — the one that always looks tidy.',
    'description' => "A shaped GP numnah in black. It does not show dust or a stray hoof mark the way a white one does, which makes it the pad most people reach for day to day.",
    'specs'    => "Shape: general purpose\nFace: quilted cotton\nAttachment: girth and billet straps\nCare: machine washable",
    'sizing'   => 'Available in cob and full.',
    'images'   => [
        ['1S3A5517.png', 'Black GP numnah laid flat'],
    ],
],
[
    'name'     => 'Quilted GP Numnah — Navy',
    'category' => 'saddle-pads-blankets',
    'short'    => 'A shaped GP numnah in navy.',
    'description' => "A shaped GP numnah in navy, quilted cotton with a soft lining and girth and billet straps.",
    'specs'    => "Shape: general purpose\nFace: quilted cotton\nAttachment: girth and billet straps\nCare: machine washable",
    'sizing'   => 'Available in cob and full.',
    'images'   => [
        ['1S3A5518.png', 'Navy GP numnah laid flat'],
        ['1S3A5527.png', 'Close detail of the navy quilting'],
    ],
],
[
    'name'     => 'Dressage Square — White',
    'category' => 'saddle-pads-blankets',
    'featured' => true,
    'short'    => 'A crisp white dressage square for the competition ring.',
    'description' => "A square-cut dressage pad in white — correct turnout for a dressage test and smart enough for a clinic. Quilted cotton with a wicking lining that pulls sweat away from the back.\n\nWashes clean without going grey, which is more than can be said for some.",
    'specs'    => "Shape: dressage square\nFace: quilted cotton\nLining: wicking\nAttachment: girth and billet straps\nCare: machine washable",
    'sizing'   => 'Available in cob and full.',
    'images'   => [
        ['1S3A5521.png', 'White dressage square laid flat'],
    ],
],
[
    'name'     => 'Dressage Square — Black',
    'category' => 'saddle-pads-blankets',
    'short'    => 'A square-cut dressage pad in black, for schooling.',
    'description' => "A square-cut dressage pad in black — the everyday schooling pad, keeping the white one clean for competition day.",
    'specs'    => "Shape: dressage square\nFace: quilted cotton\nAttachment: girth and billet straps\nCare: machine washable",
    'sizing'   => 'Available in cob and full.',
    'images'   => [
        ['1S3A5525.png', 'Black dressage square laid flat'],
    ],
],

// =====================================================================
//  GROOMING
// =====================================================================
[
    'name'     => 'Ezi-Groom Body Brush',
    'category' => 'grooming-kits-supplies',
    'brand'    => 'Shires',
    'featured' => true,
    'short'    => 'A soft body brush with a chunky grip, from the Ezi-Groom range.',
    'description' => "A soft-bristled body brush on a moulded grip that is genuinely easier to hold than a traditional wooden back — particularly for children and for anyone with cold hands on an early morning.\n\nUsed with a curry comb to lift dust out of the coat and lay it flat.",
    'specs'    => "Bristle: soft, for body brushing\nGrip: moulded, easy-hold\nRange: Ezi-Groom by Shires\nCare: rinse and air dry",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5430.jpg', 'Body brush from the side, showing the bristles'],
        ['1S3A5432.jpg', 'Ezi-Groom by Shires branding on the back'],
        ['1S3A5435.jpg', 'Bristle detail from above'],
    ],
],
[
    'name'     => 'Ezi-Groom Dandy Brush',
    'category' => 'grooming-kits-supplies',
    'brand'    => 'Shires',
    'short'    => 'A stiff dandy brush for lifting mud and dried sweat.',
    'description' => "A stiff-bristled dandy brush for getting dried mud and sweat off the coat before you start with the softer brushes. The moulded grip takes the effort out of it.\n\nToo stiff for the face or any clipped area — keep it for the body and legs.",
    'specs'    => "Bristle: stiff, for mud and dried sweat\nGrip: moulded, easy-hold\nRange: Ezi-Groom by Shires\nCare: rinse and air dry",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5439.png', 'Dandy brush from the side'],
        ['1S3A5440.png', 'Ezi-Groom branding on the back of the brush'],
        ['1S3A5443.jpg', 'The moulded grip from above'],
        ['1S3A5445.jpg', 'Close detail of the stiff bristles'],
    ],
],
[
    'name'     => 'Body Brushes — Colour Range',
    'category' => 'grooming-kits-supplies',
    'short'    => 'Soft body brushes with a moulded grip, in a choice of colours.',
    'description' => "Soft body brushes with a moulded grip, stocked in a range of colours so every horse on the yard can have its own. Keeping one kit per horse is the simplest way to stop skin conditions moving between animals.",
    'specs'    => "Bristle: soft, for body brushing\nGrip: moulded with a hand strap\nColours: several, subject to stock",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5474.1.jpg', 'Body brushes in blue, green and red'],
    ],
],
[
    'name'     => 'Dandy Brushes — Colour Range',
    'category' => 'grooming-kits-supplies',
    'short'    => 'Stiff dandy brushes in a choice of colours.',
    'description' => "Stiff-bristled dandy brushes for mud and dried sweat, stocked in several colours. A hard-working brush that earns its keep on a wet-season yard.",
    'specs'    => "Bristle: stiff\nColours: several, subject to stock",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5479.jpg', 'Dandy brushes in yellow, red and navy'],
    ],
],
[
    'name'     => 'Rubber Curry Comb',
    'category' => 'grooming-kits-supplies',
    'featured' => true,
    'short'    => 'A flexible rubber curry comb with an adjustable hand strap.',
    'description' => "A flexible rubber curry comb used in a circular motion to lift dust, loose hair and scurf out of the coat before body brushing. The adjustable strap holds it against the palm so you are not gripping it all the way round the horse.\n\nThe single most useful item in any grooming kit.",
    'specs'    => "Material: flexible rubber\nStrap: adjustable hand strap\nUse: circular motion, body only\nCare: rinse clean",
    'sizing'   => 'One size, adjustable strap.',
    'images'   => [
        ['1S3A5453.jpg', 'Curry comb from the front with the strap flat'],
        ['1S3A5454.jpg', 'Angled view showing the rubber teeth'],
        ['1S3A5456.jpg', 'The teeth face, from above'],
        ['1S3A5461.jpg', 'The smooth back of the comb'],
        ['1S3A5463.jpg', 'Side profile showing the strap mounting'],
        ['1S3A5466.jpg', 'Close detail of the moulded rubber teeth'],
    ],
],
[
    'name'     => 'Rubber Curry Combs — Colour Range',
    'category' => 'grooming-kits-supplies',
    'short'    => 'Rubber curry combs in a choice of colours.',
    'description' => "The same flexible rubber curry comb, stocked in a range of colours. Useful for colour-coding a kit per horse on a busy yard.",
    'specs'    => "Material: flexible rubber\nStrap: adjustable hand strap\nColours: several, subject to stock",
    'sizing'   => 'One size, adjustable strap.',
    'images'   => [
        ['1S3A5482.png', 'Curry combs in blue, black and purple'],
        ['1S3A5486.png', 'Oval curry combs in black, red and purple'],
    ],
],
[
    'name'     => 'Mane and Tail Brush',
    'category' => 'grooming-kits-supplies',
    'short'    => 'A cushioned brush for working through mane and tail without breaking hair.',
    'description' => "A cushioned brush with rounded pins for working through a mane and tail. Start at the bottom and work up, and you will keep far more tail than you will with a comb.\n\nAvailable in several colours.",
    'specs'    => "Pins: rounded, cushioned pad\nHandle: moulded grip\nColours: several, subject to stock",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5468.jpg', 'Mane and tail brush from the front'],
        ['1S3A5472.jpg', 'The brush shown in orange, pink and black'],
    ],
],
[
    'name'     => 'Stainless Steel Mane Comb',
    'category' => 'grooming-kits-supplies',
    'short'    => 'A traditional metal mane comb for pulling and plaiting.',
    'description' => "A solid stainless steel mane comb for pulling a mane and for sectioning it up when plaiting. Small, cheap and it will outlast almost everything else in the kit.",
    'specs'    => "Material: stainless steel\nUse: mane pulling and plaiting",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5446.jpg', 'Stainless steel mane comb'],
    ],
],
[
    'name'     => 'Ezi-Groom Plaiting Bands',
    'category' => 'grooming-kits-supplies',
    'brand'    => 'Shires',
    'short'    => 'Plaiting bands in white and black, sold by the bag.',
    'description' => "Plaiting bands for manes and tails, sold in generous bags. White for a light mane, black for a dark one — matched properly they disappear completely in the plait.\n\nBuy more than you think you need; they are never where you left them.",
    'specs'    => "Colours: white and black\nQuantity: bagged\nRange: Ezi-Groom by Shires",
    'sizing'   => 'One size.',
    'images'   => [
        ['1S3A5489.png', 'A bag of white plaiting bands'],
        ['1S3A5498.png', 'Black and white bands shown together'],
    ],
],
[
    'name'     => 'HY Grooming and Tack Box',
    'category' => 'stable-equipment',
    'brand'    => 'HY',
    'featured' => true,
    'short'    => 'A sturdy plastic tack box with a lift-out tray and a lockable catch.',
    'description' => "A hard-wearing plastic grooming and tack box with a metal catch and a lift-out tray inside, so brushes stay on top and the heavier items sit underneath. Stackable, which matters in a crowded tack room.\n\nAvailable in a range of lid colours.",
    'specs'    => "Material: hard-wearing moulded plastic\nCatch: metal, lockable\nInterior: lift-out tray\nStackable: yes\nMaker: HY",
    'sizing'   => 'One size. Stackable with others in the range.',
    'images'   => [
        ['1S3A5503.png', 'Tack box in navy with a pink lid'],
        ['1S3A5509.png', 'The range stacked, showing the lid colours'],
    ],
],

// =====================================================================
//  BREECHES & APPAREL
// =====================================================================
[
    'name'     => 'Beige Riding Breeches',
    'category' => 'breeches-tights',
    'featured' => true,
    'short'    => 'Classic beige breeches with a knee patch — correct for the show ring.',
    'description' => "Beige breeches cut for riding, with a shaped knee patch and enough stretch to be comfortable through a long schooling session. Beige is correct turnout for showing and most competition, and smart enough to teach in.",
    'specs'    => "Colour: beige\nPatch: knee patch\nFabric: stretch riding fabric\nWaist: mid rise",
    'sizing'   => 'Take your usual size. Riding breeches are cut close — if you are between sizes, try both.',
    'images'   => [
        ['1S3A5643.png', 'Beige breeches folded, showing the knee patch'],
        ['1S3A5656.png', 'The breeches laid flat, full length'],
    ],
],
[
    'name'     => 'Navy Riding Breeches',
    'category' => 'breeches-tights',
    'short'    => 'Navy breeches with a contrast panel, for schooling.',
    'description' => "Navy breeches with a bright contrast panel down the leg — a schooling breech rather than a competition one, and hard-wearing enough for daily use.",
    'specs'    => "Colour: navy with contrast panel\nFabric: stretch riding fabric\nWaist: mid rise",
    'sizing'   => 'Take your usual size.',
    'images'   => [
        ['1S3A5647.png', 'Navy breeches folded, showing the contrast panel'],
        ['1S3A5648.png', 'The breeches laid flat'],
    ],
],
[
    'name'     => 'Black Full Seat Breeches',
    'category' => 'breeches-tights',
    'short'    => 'Black breeches with a full grip seat, for flatwork.',
    'description' => "Black breeches with a full grip seat — the patterned panel runs from the knee up through the seat, holding the rider in the saddle without gluing them to it. The choice for dressage and serious flatwork.",
    'specs'    => "Colour: black\nSeat: full grip seat\nFabric: four-way stretch\nWaist: mid rise",
    'sizing'   => 'Take your usual size. Full seat breeches are cut close by design.',
    'images'   => [
        ['1S3A5654.png', 'Black breeches laid flat, showing the full grip seat'],
    ],
],

// =====================================================================
//  FOOTWEAR
// =====================================================================
[
    'name'     => 'HY Leather Jodhpur Boots — Black',
    'category' => 'footwear',
    'brand'    => 'HY',
    'featured' => true,
    'short'    => 'Black leather jodhpur boots with elasticated sides.',
    'description' => "Leather jodhpur boots in black, with elasticated side gussets and a pull tab so they go on easily. Worn on their own for schooling or under half chaps for longer work.\n\nA proper leather boot will mould to the foot and last; a synthetic one rarely does either.",
    'specs'    => "Upper: leather\nSides: elasticated gussets\nSole: rubber, riding heel\nMaker: HY\nColour: black",
    'sizing'   => 'Measure the foot flat, heel to longest toe, late in the day. Between sizes, take the larger.',
    'images'   => [
        ['1S3A5661.png', 'Black jodhpur boot from the side'],
        ['1S3A5664.png', 'A pair shown together'],
    ],
],
[
    'name'     => 'HY Leather Jodhpur Boots — Brown',
    'category' => 'footwear',
    'brand'    => 'HY',
    'short'    => 'The same leather jodhpur boot in warm brown.',
    'description' => "Leather jodhpur boots in brown, with elasticated sides and a pull tab. Brown boots suit brown tack and are the traditional choice for showing and Pony Club.",
    'specs'    => "Upper: leather\nSides: elasticated gussets\nSole: rubber, riding heel\nMaker: HY\nColour: brown",
    'sizing'   => 'Measure the foot flat, heel to longest toe, late in the day. Between sizes, take the larger.',
    'images'   => [
        ['1S3A5668.png', 'Brown jodhpur boot from the side'],
        ['1S3A5670.png', 'Angled view showing the pull tabs'],
        ['1S3A5671.png', 'A pair from the rear'],
        ['1S3A5673.png', 'A pair from the front, showing the HY tab'],
    ],
],
[
    'name'     => 'Black Half Chaps',
    'category' => 'footwear',
    'short'    => 'Zip-up half chaps in black, worn over jodhpur boots.',
    'description' => "Half chaps in black, worn over jodhpur boots to protect the lower leg and give the grip a long boot would. A rear zip with a guard keeps the pull tab clear of the girth.",
    'specs'    => "Closure: rear zip with guard\nColour: black\nUse: worn over jodhpur boots",
    'sizing'   => 'Measure the calf at its widest point and from the floor to the back of the knee, wearing your riding boots.',
    'images'   => [
        ['1S3A5676.png', 'Black half chaps folded, showing the zip'],
    ],
],
[
    'name'     => 'Brown Leather Gaiters',
    'category' => 'footwear',
    'short'    => 'Leather gaiters in brown, studded, for a smarter turnout.',
    'description' => "Leather gaiters in brown, fastening with studs rather than a zip. Worn over jodhpur boots they give the clean line of a long boot at a fraction of the cost, and they break in beautifully.",
    'specs'    => "Material: leather\nClosure: studs\nColour: brown\nUse: worn over jodhpur boots",
    'sizing'   => 'Measure the calf at its widest point and from the floor to the back of the knee, wearing your riding boots.',
    'images'   => [
        ['1S3A5681.png', 'A pair of brown leather gaiters, showing the studs'],
    ],
],

// =====================================================================
//  COMPETITION & WHIPS
// =====================================================================
[
    'name'     => 'Competition Number Holder',
    'category' => 'gloves-accessories',
    'short'    => 'A clear armband holder for competition numbers.',
    'description' => "A clear plastic holder with an elastic strap, worn on the arm to display a competition number. Small, cheap, and the thing everyone forgets until the morning of the show.",
    'specs'    => "Material: clear plastic with elastic strap\nWear: on the upper arm\nIncludes: number card where shown",
    'sizing'   => 'One size, elasticated.',
    'images'   => [
        ['1S3A5685.png', 'Number holder with printed numbers and cord'],
        ['1S3A5692.png', 'The clear holder with its elastic armband'],
    ],
],
[
    'name'     => 'LeMieux Hat Silk',
    'category' => 'gloves-accessories',
    'brand'    => 'LeMieux',
    'short'    => 'A navy hat silk with a pom-pom, from LeMieux.',
    'description' => "A hat silk that pulls over a skull cap to smarten it up and keep the sun off. Navy with a contrast peak and a pom-pom on top.\n\nA silk is also the easiest way to show yard or team colours.",
    'specs'    => "Fit: pulls over a standard skull cap\nColour: navy with contrast peak\nDetail: pom-pom\nMaker: LeMieux",
    'sizing'   => 'One size, fits a standard skull cap.',
    'images'   => [
        ['1S3A5701.png', 'Navy LeMieux hat silk with the pom-pom'],
    ],
],
[
    'name'     => 'Hat Silk — Red',
    'category' => 'gloves-accessories',
    'short'    => 'A plain red hat silk for a skull cap.',
    'description' => "A plain red hat silk that pulls over a skull cap. Bright, easy to spot across a cross country course, and a simple way to run yard colours.",
    'specs'    => "Fit: pulls over a standard skull cap\nColour: red",
    'sizing'   => 'One size, fits a standard skull cap.',
    'images'   => [
        ['1S3A5703.png', 'Red hat silk on its packaging card'],
    ],
],
[
    'name'     => 'Dressage Schooling Whip',
    'category' => 'gloves-accessories',
    'short'    => 'A long schooling whip with a moulded handle.',
    'description' => "A long dressage schooling whip, light enough in the hand to be used without disturbing the rein contact. Long enough to reach behind the leg without taking the hand off the rein.",
    'specs'    => "Type: dressage / schooling whip\nHandle: moulded grip with wrist detail\nColour: navy",
    'sizing'   => 'One length. A schooling whip should reach the horse\'s hindquarters with the hand in a normal rein position.',
    'images'   => [
        ['1S3A5708.png', 'Navy dressage schooling whip, full length'],
    ],
],
[
    'name'     => 'Lunge Whip',
    'category' => 'gloves-accessories',
    'short'    => 'A long lunge whip for groundwork.',
    'description' => "A long lunge whip for working a horse on a circle. Used to guide and position rather than to chase — the length is there so you can stay at the centre of the circle and still direct the horse.",
    'specs'    => "Type: lunge whip\nColour: green\nUse: groundwork and lungeing",
    'sizing'   => 'One length.',
    'images'   => [
        ['1S3A5712.png', 'Green lunge whip, full length'],
    ],
],
[
    'name'     => 'Riding Crop',
    'category' => 'gloves-accessories',
    'short'    => 'A short jumping crop with a wrist loop.',
    'description' => "A short riding crop with a broad keeper at the end and a wrist loop on the handle. The length suits jumping and general riding, where a long schooling whip would be in the way.",
    'specs'    => "Type: short crop\nHandle: wrist loop\nEnd: broad leather keeper",
    'sizing'   => 'One length.',
    'images'   => [
        ['1S3A5715.png', 'Riding crop showing the keeper and wrist loop'],
    ],
],

];
