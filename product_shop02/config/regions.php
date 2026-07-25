<?php

/*
|--------------------------------------------------------------------------
| Delivery regions (Oman + GCC)
|--------------------------------------------------------------------------
|
| Keys are stored on orders; human labels are resolved from lang/*/regions.php
| so both Arabic and English read naturally. Oman is the default country.
|
*/

return [

    'default_country' => 'OM',

    // GCC countries — ISO 3166-1 alpha-2 codes.
    'countries' => ['OM', 'SA', 'AE', 'KW', 'QA', 'BH'],

    // The 11 governorates of Oman.
    'governorates' => [
        'muscat',
        'dhofar',
        'musandam',
        'buraimi',
        'dakhiliyah',
        'north_batinah',
        'south_batinah',
        'north_sharqiyah',
        'south_sharqiyah',
        'dhahirah',
        'wusta',
    ],
];
