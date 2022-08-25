<?php

return [
    'send_email' => false,
    'google' => [
        'recaptcha' => [
            'site_key' => env('GOOGLE_RECAPTCHA_SITEKEY', null),
            'site_secret' => env('GOOGLE_RECAPTCHA_SITESECRET', null),
        ]
    ],
	'payment_modes' =>['CASH'=>'Cash','CHEQUE'=>'Cheque','CARDSWIPE'=>'Card Swipe','DD'=>'Demand Draft','NEFT'=>'NEFT','NETBENKING'=>'Net Banking']
];
