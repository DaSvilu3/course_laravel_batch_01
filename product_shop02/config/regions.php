<?php

/*
|--------------------------------------------------------------------------
| Delivery regions (Oman wilayats + GCC)
|--------------------------------------------------------------------------
|
| Oman's governorates and their wilayats, with Arabic + English names (the
| Regions helper picks the label for the current locale). Orders store the
| governorate and wilayat keys; the governorate is derived from the wilayat.
|
*/

return [

    'default_country' => 'OM',

    'countries' => [
        'OM' => ['ar' => 'عُمان', 'en' => 'Oman'],
        'SA' => ['ar' => 'السعودية', 'en' => 'Saudi Arabia'],
        'AE' => ['ar' => 'الإمارات', 'en' => 'UAE'],
        'KW' => ['ar' => 'الكويت', 'en' => 'Kuwait'],
        'QA' => ['ar' => 'قطر', 'en' => 'Qatar'],
        'BH' => ['ar' => 'البحرين', 'en' => 'Bahrain'],
    ],

    'governorates' => [
        'muscat' => [
            'name' => ['ar' => 'مسقط', 'en' => 'Muscat'],
            'wilayats' => [
                'muscat' => ['ar' => 'مسقط', 'en' => 'Muscat'],
                'muttrah' => ['ar' => 'مطرح', 'en' => 'Muttrah'],
                'al_amerat' => ['ar' => 'العامرات', 'en' => 'Al Amerat'],
                'bawshar' => ['ar' => 'بوشر', 'en' => 'Bawshar'],
                'al_seeb' => ['ar' => 'السيب', 'en' => 'Al Seeb'],
                'quriyat' => ['ar' => 'قريات', 'en' => 'Quriyat'],
            ],
        ],
        'dhofar' => [
            'name' => ['ar' => 'ظفار', 'en' => 'Dhofar'],
            'wilayats' => [
                'salalah' => ['ar' => 'صلالة', 'en' => 'Salalah'],
                'taqah' => ['ar' => 'طاقة', 'en' => 'Taqah'],
                'mirbat' => ['ar' => 'مرباط', 'en' => 'Mirbat'],
                'rakhyout' => ['ar' => 'رخيوت', 'en' => 'Rakhyout'],
                'thamrait' => ['ar' => 'ثمريت', 'en' => 'Thamrait'],
                'dhalkout' => ['ar' => 'ضلكوت', 'en' => 'Dhalkout'],
                'al_mazyona' => ['ar' => 'المزيونة', 'en' => 'Al Mazyona'],
                'maqshan' => ['ar' => 'مقشن', 'en' => 'Maqshan'],
                'shalim' => ['ar' => 'شليم وجزر الحلانيات', 'en' => 'Shalim and the Hallaniyat Islands'],
                'sadah' => ['ar' => 'سدح', 'en' => 'Sadah'],
            ],
        ],
        'musandam' => [
            'name' => ['ar' => 'مسندم', 'en' => 'Musandam'],
            'wilayats' => [
                'khasab' => ['ar' => 'خصب', 'en' => 'Khasab'],
                'dibba' => ['ar' => 'دبا', 'en' => 'Dibba'],
                'bukha' => ['ar' => 'بخا', 'en' => 'Bukha'],
                'madha' => ['ar' => 'مدحا', 'en' => 'Madha'],
            ],
        ],
        'buraimi' => [
            'name' => ['ar' => 'البُريمي', 'en' => 'Al Buraimi'],
            'wilayats' => [
                'al_buraimi' => ['ar' => 'البُريمي', 'en' => 'Al Buraimi'],
                'mahdah' => ['ar' => 'محضة', 'en' => 'Mahdah'],
                'al_sinainah' => ['ar' => 'السنينة', 'en' => 'Al Sinainah'],
            ],
        ],
        'dakhiliyah' => [
            'name' => ['ar' => 'الداخلية', 'en' => 'Ad Dakhiliyah'],
            'wilayats' => [
                'nizwa' => ['ar' => 'نزوى', 'en' => 'Nizwa'],
                'bahla' => ['ar' => 'بهلاء', 'en' => 'Bahla'],
                'manah' => ['ar' => 'منح', 'en' => 'Manah'],
                'al_hamra' => ['ar' => 'الحمراء', 'en' => 'Al Hamra'],
                'adam' => ['ar' => 'أدم', 'en' => 'Adam'],
                'izki' => ['ar' => 'ازكي', 'en' => 'Izki'],
                'samail' => ['ar' => 'سمائل', 'en' => 'Samail'],
                'bidbid' => ['ar' => 'بدبد', 'en' => 'Bidbid'],
            ],
        ],
        'north_batinah' => [
            'name' => ['ar' => 'شمال الباطنة', 'en' => 'North Al Batinah'],
            'wilayats' => [
                'sohar' => ['ar' => 'صحار', 'en' => 'Sohar'],
                'shinas' => ['ar' => 'شناص', 'en' => 'Shinas'],
                'liwa' => ['ar' => 'لوى', 'en' => 'Liwa'],
                'saham' => ['ar' => 'صحم', 'en' => 'Saham'],
                'al_khaburah' => ['ar' => 'الخابورة', 'en' => 'Al Khaburah'],
                'al_suwayq' => ['ar' => 'السويق', 'en' => 'Al Suwayq'],
            ],
        ],
        'south_batinah' => [
            'name' => ['ar' => 'جنوب الباطنة', 'en' => 'South Al Batinah'],
            'wilayats' => [
                'al_rustaq' => ['ar' => 'الرستاق', 'en' => 'Al Rustaq'],
                'al_awabi' => ['ar' => 'العوابي', 'en' => 'Al Awabi'],
                'nakhal' => ['ar' => 'نخل', 'en' => 'Nakhal'],
                'wadi_al_maawil' => ['ar' => 'وادي المعاول', 'en' => 'Wadi Al Maawil'],
                'barka' => ['ar' => 'بركاء', 'en' => 'Barka'],
                'al_musannah' => ['ar' => 'المصنعة', 'en' => 'Al Musannah'],
            ],
        ],
        'north_sharqiyah' => [
            'name' => ['ar' => 'شمال الشرقية', 'en' => 'North Ash Sharqiyah'],
            'wilayats' => [
                'ibra' => ['ar' => 'إبراء', 'en' => 'Ibra'],
                'al_mudhaibi' => ['ar' => 'المضيبي', 'en' => 'Al Mudhaibi'],
                'bidiyah' => ['ar' => 'بدية', 'en' => 'Bidiyah'],
                'al_qabil' => ['ar' => 'القابل', 'en' => 'Al Qabil'],
                'wadi_bani_khalid' => ['ar' => 'وادي بني خالد', 'en' => 'Wadi Bani Khalid'],
                'dima_wa_tayeen' => ['ar' => 'دماء والطائيين', 'en' => 'Dima Wa Tayeen'],
            ],
        ],
        'south_sharqiyah' => [
            'name' => ['ar' => 'جنوب الشرقية', 'en' => 'South Ash Sharqiyah'],
            'wilayats' => [
                'sur' => ['ar' => 'صور', 'en' => 'Sur'],
                'al_kamil_wal_wafi' => ['ar' => 'الكامل والوافي', 'en' => 'Al Kamil Wal Wafi'],
                'jalan_bani_bu_hasan' => ['ar' => 'جعلان بني بو حسن', 'en' => 'Jalan Bani Bu Hasan'],
                'jalan_bani_bu_ali' => ['ar' => 'جعلان بني بو علي', 'en' => 'Jalan Bani Bu Ali'],
                'masirah' => ['ar' => 'مصيرة', 'en' => 'Masirah'],
            ],
        ],
        'dhahirah' => [
            'name' => ['ar' => 'الظاهرة', 'en' => 'Adh Dhahirah'],
            'wilayats' => [
                'ibri' => ['ar' => 'عبري', 'en' => 'Ibri'],
                'yanqul' => ['ar' => 'ينقل', 'en' => 'Yanqul'],
                'dhank' => ['ar' => 'ضنك', 'en' => 'Dhank'],
            ],
        ],
        'wusta' => [
            'name' => ['ar' => 'الوسطى', 'en' => 'Al Wusta'],
            'wilayats' => [
                'haima' => ['ar' => 'هيما', 'en' => 'Haima'],
                'mahout' => ['ar' => 'محوت', 'en' => 'Mahout'],
                'duqm' => ['ar' => 'الدقم', 'en' => 'Duqm'],
                'al_jazer' => ['ar' => 'الجازر', 'en' => 'Al Jazer'],
            ],
        ],
    ],
];
