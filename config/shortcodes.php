<?php

return [
    'magic_keywords' => [
        [
            'column' => 'firstname',
            'shortcode' => '{firstname}',
            'description' => '',
        ],
        [
            'column' => 'middlename',
            'shortcode' => '{middlename}',
            'description' => '',
        ],
        [
            'column' => 'surname',
            'shortcode' => '{surname}',
            'description' => '',
        ],
        [
            'column' => 'mobileno',
            'shortcode' => '{mobileno}',
            'description' => '',
        ],
        [
            'column' => 'alt_mobileno',
            'shortcode' => '{alt_mobileno}',
            'description' => '',
        ],
        [
            'column' => 'email',
            'shortcode' => '{email}',
            'description' => '',
        ],
		[
            'column' => 'company_name',
            'shortcode' => '{{#company_name}}',
            'description' => '',
        ],
		/* [
            'column' => 'name',
            'shortcode' => '{{#company_contact_person_name}}',
            'description' => '',
        ],
		[
            'column' => 'mobileno',
            'shortcode' => '{{#company_contact_person_mobile}}',
            'description' => '',
        ],
		[
            'column' => 'email',
            'shortcode' => '{{#company_contact_person_email}}',
            'description' => '',
        ], */
		[
            'column' => 'gst_no',
            'shortcode' => '{{#company_gst_no}}',
            'description' => '',
        ],
		[
            'column' => 'client_data->name',
            'shortcode' => '{{#client_name}}',
            'description' => '',
        ],
		[
            'column' => 'client_data->email',
            'shortcode' => '{{#client_email}}',
            'description' => '',
        ],
		[
            'column' => 'client_data->mobile_no',
            'shortcode' => '{{#client_mobile}}',
            'description' => '',
        ],
		[
            'column' => 'subscriptions_uid',
            'shortcode' => '{{#subscription_invoice_no}}',
            'description' => '',
        ],
		[
            'column' => 'total_amount',
            'shortcode' => '{{#subscription_total_amount}}',
            'description' => '',
        ],
		[
            'column' => 'sgst_amount',
            'shortcode' => '{{#subscription_sgst_amount}}',
            'description' => '',
        ],
		[
            'column' => 'sgst',
            'shortcode' => '{{#subscription_sgst}}',
            'description' => '',
        ],
		[
            'column' => 'cgst',
            'shortcode' => '{{#subscription_cgst}}',
            'description' => '',
        ],
		[
            'column' => 'cgst_amount',
            'shortcode' => '{{#subscription_cgst_amount}}',
            'description' => '',
        ],
		[
            'column' => 'igst',
            'shortcode' => '{{#subscription_igst}}',
            'description' => '',
        ],
		[
            'column' => 'igst_amount',
            'shortcode' => '{{#subscription_igst_amount}}',
            'description' => '',
        ],
		[
            'column' => 'payment_mode',
            'shortcode' => '{{#subscription_payment_mode}}',
            'description' => '',
        ],
		[
            'column' => 'payment_date',
            'shortcode' => '{{#subscription_payment_date}}',
            'description' => '',
        ],
		[
            'column' => 'payment_bank_name',
            'shortcode' => '{{#subscription_bank_name}}',
            'description' => '',
        ],
		[
            'column' => 'payment_number',
            'shortcode' => '{{#subscription_transaction_no}}',
            'description' => '',
        ],
		[
            'column' => 'subscription_expiry_date',
            'shortcode' => '{{#subscription_expiry_date}}',
            'description' => '',
        ],
		[
            'column' => 'name',
            'shortcode' => '{{#user_name}}',
            'description' => '',
        ],
		[
            'column' => 'email',
            'shortcode' => '{{#user_email}}',
            'description' => '',
        ],
		[
            'column' => 'mobileno',
            'shortcode' => '{{#user_mobile}}',
            'description' => '',
        ],
		[
            'column' => 'name',
            'shortcode' => '{{#dealer_name}}',
            'description' => '',
        ],
		[
            'column' => 'email',
            'shortcode' => '{{#dealer_email}}',
            'description' => '',
        ],
		[
            'column' => 'mobileno',
            'shortcode' => '{{#dealer_mobile}}',
            'description' => '',
        ],
		[
            'column' => 'commission',
            'shortcode' => '{{#dealer_commission}}',
            'description' => '',
        ],
		[
            'column' => 'name',
            'shortcode' => '{{#distributor_name}}',
            'description' => '',
        ],
		[
            'column' => 'email',
            'shortcode' => '{{#distributor_email}}',
            'description' => '',
        ],
		[
            'column' => 'mobileno',
            'shortcode' => '{{#distributor_mobile}}',
            'description' => '',
        ],
		[
            'column' => 'commission',
            'shortcode' => '{{#distributor_commission}}',
            'description' => '',
        ],
    ],
];
