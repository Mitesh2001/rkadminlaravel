<?php
// Aside menu
return [
    'items' => [
        // Dashboard
        [
            'title' => 'Dashboard',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => 'admin./',
            'new-tab' => false,
        ],
		[
            'title' => 'Clients',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Clients',
                    'bullet' => 'dot',
					'page' => 'admin.clients.index',
                ],
                [
                    'title' => 'New Client',
                    'bullet' => 'dot',
					'page' => 'admin.clients.create',
                ]
            ]
        ],
		// [
        //     'title' => 'Subscriptions',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'All Subscriptions',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.subscriptions.index',
        //         ],
        //         [
        //             'title' => 'New Subscriptions',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.subscriptions.create',
        //         ]
        //     ]
        // ],
		// [
        //     'title' => 'Products',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'All Products',
        //             'bullet' => 'dot',
		// 			'url' => '/rkadmin/products/',
        //         ],
        //         [
        //             'title' => 'New Product',
        //             'bullet' => 'dot',
		// 			'url' => '/rkadmin/products/create',
        //         ]
        //     ]
        // ],
		// [
        //     'title' => 'Services',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'All Services',
        //             'bullet' => 'dot',
		// 			'url' => '/rkadmin/services/',
        //         ],
        //         [
        //             'title' => 'New Service',
        //             'bullet' => 'dot',
		// 			'url' => '/rkadmin/services/create',
        //         ]
        //     ]
        // ],
		// [
        //     'title' => 'Employees',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'All Employees',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.employees.index',
        //         ],
        //         [
        //             'title' => 'New Employee',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.employees.create',
        //         ]
        //     ]
        // ],
		[
            'title' => 'Users',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Users',
                    'bullet' => 'dot',
					'page' => 'admin.employees.index',
                ],
                [
                    'title' => 'New User',
                    'bullet' => 'dot',
					'page' => 'admin.employees.create',
                ]
            ]
        ],
		[
            'title' => 'Admin Roles',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Roles',
                    'bullet' => 'dot',
					'url' => '/rkadmin/roles?type=web',
                ],
                [
                    'title' => 'New Role',
                    'bullet' => 'dot',
					'url' => '/rkadmin/roles/create?type=web',
                ]
            ]
        ],
        [
            'title' => 'Fronted Roles',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Roles',
                    'bullet' => 'dot',
					'url' => '/rkadmin/roles?type=api',
                ],
                [
                    'title' => 'New Role',
                    'bullet' => 'dot',
					'url' => '/rkadmin/roles/create?type=api',
                ]
            ]
        ],
        // [
        //     'title' => 'Permissions',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'All Permissions',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.permissions.index',
        //         ],
        //         [
        //             'title' => 'New Permission',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.permissions.create',
        //         ]
        //     ]
        // ],
        [
            'title' => 'Plans',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Plans',
                    'bullet' => 'dot',
					'page' => 'admin.plan',
                ],
                [
                    'title' => 'New Plan',
                    'bullet' => 'dot',
					'page' => 'admin.plan.create',
                ]
            ]
        ],
        [
            'title' => 'All Requisitions',
            'root' => true,
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'page' => 'admin.requisitions',
            'new-tab' => false,
        ],
        // [
        //     'title' => 'Product Category',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'All Product Category',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.product-category',
        //         ],
        //         [
        //             'title' => 'New Product Category',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.product-category.create',
        //         ]
        //     ]
        // ],
        // [
        //     'title' => 'Custom Form',
        //     'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
        //     'bullet' => 'line',
        //     'root' => true,
        //     'submenu' => [
        //         [
        //             'title' => 'Add',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.custom-form.create',
        //         ],
        //         [
        //             'title' => 'Manage',
        //             'bullet' => 'dot',
		// 			'page' => 'admin.custom-form',
        //         ]
        //     ]
        // ],
        [
            'title' => 'Invoice',
            'root' => true,
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'url' => '/rkadmin/invoice',
            'new-tab' => false,
        ],
        [
            'title' => 'Emails Template',
            'icon' => 'media/svg/icons/Communication/Mail.svg',
            /* 'root' => true,
            'url' => '/rkadmin/emails',
            'new-tab' => false, */
			'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'All Emails Templates',
                    'bullet' => 'dot',
					'page' => 'admin.emails.index',
                ],
                [
                    'title' => 'New Emails Template',
                    'bullet' => 'dot',
					'page' => 'admin.emails.create',
                ]
            ]
        ],
        [
            'title' => 'Subscriptions',
            'root' => true,
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'url' => '/rkadmin/subscriptions/all',
            'new-tab' => false,
        ],
        [
            'title' => 'Commissions',
            'root' => true,
            'icon' => 'media/svg/icons/Shopping/Dollar.svg',
            'url' => '/rkadmin/commissions',
            'new-tab' => false,
        ],
        [
            'title' => 'Email SMS Log',
            'root' => true,
            'icon' => 'media/svg/icons/Code/Puzzle.svg',
            'url' => '/rkadmin/email-and-sms-log',
            'new-tab' => false,
        ],
		[
            'title' => 'Master Modules',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'dot',
            'root' => true,
			'submenu' => [
				[
                    'title' => 'Category',
                    'bullet' => 'dot',
                    'submenu' => [
						[
							'title' => 'All Categories',
							'bullet' => 'dot',
							'url' => '/rkadmin/master/product-category/',
						],
						[
							'title' => 'New Category',
							'bullet' => 'dot',
							'url' => '/rkadmin/master/product-category/create',
						]
					]
                ],
                [
                    'title' => 'Products / Services',
                    'bullet' => 'dot',
                    'submenu' => [
						[
							'title' => 'All Products / Services',
							'bullet' => 'dot',
							'url' => '/rkadmin/master/products/',
						],
						[
							'title' => 'New Product / Services',
							'bullet' => 'dot',
							'url' => '/rkadmin/master/products/create',
						]
					]
                ],
				// [
                //     'title' => 'Services',
                //     'bullet' => 'dot',
                //     'submenu' => [
				// 		[
				// 			'title' => 'All Services',
				// 			'bullet' => 'dot',
				// 			'url' => '/rkadmin/master/services/',
				// 		],
				// 		[
				// 			'title' => 'New Service',
				// 			'bullet' => 'dot',
				// 			'url' => '/rkadmin/master/services/create',
				// 		]
				// 	]
                // ],
				[
                    'title' => 'Notice Board',
                    'bullet' => 'dot',
                    'submenu' => [
						[
							'title' => 'All Notice',
							'bullet' => 'dot',
							'url' => '/rkadmin/notice-board',
						],
						[
							'title' => 'New Notice',
							'bullet' => 'dot',
							'url' => '/rkadmin/notice-board/create',
						]
					]
                ],
				[
                    'title' => 'Announcement',
                    'bullet' => 'dot',
                    'submenu' => [
						[
							'title' => 'All Announcement',
							'bullet' => 'dot',
							'url' => '/rkadmin/announcement',
						],
						[
							'title' => 'New Announcement',
							'bullet' => 'dot',
							'url' => '/rkadmin/announcement/create',
						]
					]
                ],
			]
        ],
        [
            'title' => 'SMS API Setting',
            'root' => true,
            'icon' => 'media/svg/icons/Code/Puzzle.svg',
            'page' => 'admin.sms.index',
            'new-tab' => false,
        ],
        [
            'title' => 'Global Settings',
            'root' => true,
            'icon' => 'media/svg/icons/General/Settings-2.svg',
            'page' => 'admin.settings.index',
            'new-tab' => false,
        ],
    ]
];