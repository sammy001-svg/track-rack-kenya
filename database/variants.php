<?php
/**
 * The client's sizes & colours sheet, as data.
 *
 * One entry per row of the sheet. `products` lists every slug the row
 * applies to, across both catalogues: this site's own and the live site's,
 * which names a number of things differently. A slug that is not present
 * in the database being written to is simply skipped.
 *
 * Used by bin/import-products.php, which applies it after an import, and by
 * bin/export-variants-sql.php, which turns it into a file that can be
 * imported through phpMyAdmin without touching anything else.
 */

return [

    'Helmets'                          => [
        'variants' => [
            'Size' => ['Small', 'Medium', 'Large'],
        ],
        'products' => [
            'black-skull-cap',
            'champion-black-skull-cap',
            'hkm-velvet-riding-hat',
            'matt-black-vented-riding-helmet',
            'velvet-riding-hat',
        ],
    ],

    'Bits'                             => [
        'variants' => [
            'Size' => ['4.5"', '5"', '5.5"'],
        ],
        'products' => [
            'loose-ring-jointed-snaffle',
            'loose-ring-snaffle-with-copper-lozenge',
            'shires-loose-ring-snaffle-with-copper-lozenge',
            'shires-stainless-steel-snaffle-bit',
            'training-bit-with-rope-cheeks',
        ],
    ],

    'Stirrup Irons'                    => [
        'variants' => [
            'Size' => ['4"', '4 1/4"', '4 1/2"'],
        ],
        'products' => [
            'compositi-lightweight-stirrups',
            'korsteel-stainless-steel-stirrup-irons',
            'peacock-safety-stirrup-irons',
            'stainless-steel-stirrup-irons',
        ],
    ],

    'Girths (Standard)'                => [
        'variants' => [
            'Size' => ['42"', '44"', '46"', '48"', '50"'],
            'Colour' => ['Black', 'Brown'],
        ],
        'products' => [
            'black-elastic-girth',
            'fleece-lined-girth',
            'padded-anti-chafe-girth',
        ],
    ],

    'Girths (Dressage)'                => [
        'variants' => [
            'Size' => ['22"', '24"', '26"', '28"', '30"'],
            'Colour' => ['Black', 'Brown'],
        ],
        'products' => [
            'short-dressage-girth',
        ],
    ],

    'Numnah (Square)'                  => [
        'variants' => [
            'Size' => ['17" — 18"'],
            'Colour' => ['White', 'Green', 'Red', 'Blue'],
        ],
        'products' => [
            'dressage-square-black',
            'dressage-square-white',
        ],
    ],

    'Numnah (Shaped)'                  => [
        'variants' => [
            'Size' => ['15" — 16 1/2"', '17" — 18"'],
            'Colour' => ['Red', 'Blue', 'Green', 'Black', 'Teal'],
        ],
        'products' => [
            'quilted-gp-numnah',
            'quilted-gp-numnah-2',
            'quilted-gp-numnah-black',
            'quilted-gp-numnah-navy',
            'quilted-gp-numnah-red',
            'quilted-gp-numnah-teal',
            'quilted-gp-numnah-various-colours',
        ],
    ],

    'Brushing Boots'                   => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
            'Colour' => ['Red', 'Green', 'Black', 'Grey'],
        ],
        'products' => [
            'anatomic-brushing-boots',
            'brushing-boots',
            'brushing-boots-2',
        ],
    ],

    'Overreach Boots (Neoprene)'       => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
            'Colour' => ['Black', 'Red', 'Green', 'Blue'],
        ],
        'products' => [
            'neoprene-overreach-boots',
            'overreach-boots-red',
        ],
    ],

    'Overreach Boots (Rubber)'         => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
        ],
        'products' => [
            'rubber-overreach-boots',
            'rubber-pull-on-overreach-boots-or-velcro-closure',
        ],
    ],

    'Bandages'                         => [
        'variants' => [
            'Size' => ['One size'],
            'Pack' => ['x4 per packet'],
            'Colour' => ['Navy blue', 'Grey', 'Lime green', 'White', 'Royal blue'],
        ],
        'products' => [
            'fleece-polo-stable-bandages',
        ],
    ],

    'Shoes (Racing Plates / Alumites)' => [
        'variants' => [
            'Size' => ['4', '5', '6'],
        ],
        'products' => [
            'aluminium-horseshoes',
        ],
    ],

    'Shoes (Steel)'                    => [
        'variants' => [
            'Size' => ['3', '4', '5', '6', '7', '8'],
        ],
        'products' => [
            'steel-horseshoes',
        ],
    ],

    'Nails'                            => [
        'variants' => [
            'Size' => ['E3', 'E4', 'Crown Head I', 'ASV 1 3/4', 'FJ1'],
            'Pack' => ['Packet of 50', 'Box of 250'],
        ],
        'products' => [
            'optima-horseshoe-nails',
        ],
    ],

    'Dubbin'                           => [
        'variants' => [
            'Size' => ['250ml tub', '500ml tub'],
        ],
        'products' => [
            'dubbin',
        ],
    ],

    'Leather Soap'                     => [
        'variants' => [
            'Size' => ['Bar', '500ml tub'],
        ],
        'products' => [
            'glycerine-bar-leather-saddle-soap',
            'glycerine-leather-saddle-soap',
            'leather-saddle-soap',
            'leather-soap',
        ],
    ],

    'Leather Dressing'                 => [
        'variants' => [
            'Size' => ['500ml', '1 litre'],
        ],
        'products' => [
            'leather-dressing',
        ],
    ],

    'Clay'                             => [
        'variants' => [
            'Size' => ['2kg', '4kg'],
        ],
        'products' => [
            'natural-clay',
        ],
    ],

    'Hoof Oil'                         => [
        'variants' => [
            'Size' => ['500ml', '1 litre'],
        ],
        'products' => [
            'hoof-oil',
        ],
    ],

    'Stockholm Tar'                    => [
        'variants' => [
            'Size' => ['500ml', '1 litre'],
        ],
        'products' => [
            'stockholm-tar',
        ],
    ],

    'Summer Spray'                     => [
        'variants' => [
            'Size' => ['1 litre', '5 litres'],
        ],
        'products' => [
            'vetsbrands-summer-spray',
        ],
    ],

    'Buzz Off'                         => [
        'variants' => [
            'Size' => ['750ml', '5 litres'],
        ],
        'products' => [
            'buzz-off-fly-repellent',
        ],
    ],

    'Equimins Fly Repellent'           => [
        'variants' => [
            'Size' => ['750ml', '5 litres'],
        ],
        'products' => [
            'equimins-fly-midge-repellent',
        ],
    ],

    'Lemon Shampoo'                    => [
        'variants' => [
            'Size' => ['500ml', '5 litres'],
        ],
        'products' => [
            'endeavon-lemon-shampoo',
            'lemon-shampoo',
        ],
    ],

    'Mane, Coat & Tail'                => [
        'variants' => [
            'Size' => ['500ml', '2.5 litres', '5 litres'],
        ],
        'products' => [
            'gold-label-mane-coat-tail-lotion',
        ],
    ],

    'Head Collars'                     => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
            'Colour' => ['Red', 'Navy', 'Black', 'Green', 'Sky Blue', 'Purple'],
        ],
        'products' => [
            'nylon-headcollar-assorted-colours',
            'nylon-headcollar-colour-range',
        ],
    ],

    'Lead Ropes'                       => [
        'variants' => [
            'Length' => ['3m'],
            'Colour' => ['Red', 'Green', 'Black', 'Purple', 'Sky blue'],
        ],
        'products' => [
            'rope-lead-ropes',
            'rope-lead-ropes-various-colours-3m',
        ],
    ],

    'Bridles'                          => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
            'Colour' => ['Brown', 'Black'],
        ],
        'products' => [
            'black-leather-snaffle-bridle',
            'black-or-havana-leather-flash-bridle',
            'havana-leather-snaffle-bridle',
        ],
    ],

    'Martingales'                      => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
            'Colour' => ['Brown', 'Black'],
        ],
        'products' => [
            'leather-bib-martingale',
            'leather-martingale',
        ],
    ],

    'Jodhpur Boots'                    => [
        'variants' => [
            'Size' => ['Child 12', 'Child 13', 'Child 1', 'Child 2', 'Child 3', 'Adult 4', 'Adult 5', 'Adult 6', 'Adult 7', 'Adult 8'],
        ],
        'products' => [
            'hy-leather-jodhpur-boots-black',
            'hy-leather-jodhpur-boots-brown',
            'hy-leather-jodhpur-boots-brown-black',
            'jodhpur-boots-black-brown',
        ],
    ],

    'Riding Jodhpurs'                  => [
        'variants' => [
            'Fit' => ['Children', 'Maids / Boys', 'Ladies', 'Gents'],
        ],
        'products' => [
            'beige-riding-breeches',
            'black-full-seat-breeches',
            'full-seat-breeches',
            'navy-riding-breeches',
            'riding-breeches',
        ],
    ],

    'Saddles'                          => [
        'variants' => [
            'Seat size' => ['16"', '16 1/2"', '17"', '17 1/2"'],
        ],
        'products' => [
            'black-leather-general-purpose-saddle',
            'jeffries-elite-leather-saddle',
            'thorowgood-leather-saddle',
            'thorowgood-saddle',
            'wintec-2000-all-purpose-saddle',
            'wintec-synthetic-saddle',
        ],
    ],

    'Chaps (Adults)'                   => [
        'variants' => [
            'Size' => ['XS', 'Small', 'Medium', 'Large', 'XL'],
            'Colour' => ['Brown', 'Black'],
        ],
        'products' => [
            'black-half-chaps',
            'brown-leather-gaiters',
            'half-chaps',
        ],
    ],

    'Fly Masks'                        => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
            'Style' => ['With ears', 'Without ears'],
        ],
        'products' => [
            'flyguard-pro-fine-mesh-fly-mask',
        ],
    ],

    'Girths (Borghey)'                 => [
        'variants' => [
            'Size' => ['40"', '42"', '44"', '46"', '48"', '50"'],
        ],
        'products' => [
            'borghey-girth',
            'black-burghley-girth',
        ],
    ],

    'Night Rugs'                       => [
        'variants' => [
            'Size' => ['Pony', 'Cob', 'Full'],
        ],
        'products' => [
            'night-rug',
            'night-rugs',
        ],
    ],

];
