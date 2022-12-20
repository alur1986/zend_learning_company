<?php

return [
    'router' => [
        'routes' => [
            'elucidat' => [
                'type'          => 'Segment',
                'may_terminate' => true,
                'options'       => [
                    'route'    => '/elucidat',
                    'defaults' => [
                        'controller' => 'Elucidat\Controller\Manage',
                        'action'     => 'directory'
                    ]
                ],
                'child_routes'  => [
                    'launch' => [
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route'       => '/launch/:account_id/:author_id',
                            'constraints' => [
                                'account_id' => '[0-9]{1,}',
                                'author_id' => '[0-9]{1,}'
                            ],
                            'defaults'    => [
                                'action' => 'launch',
                                'controller'=>'Elucidat\Controller\Launch',
                            ]
                        ],
                    ],
                    'directory' => [
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route'       => '/[directory]',
                            'constraints' => [
                                'page' => '[0-9]+'
                            ],
                            'defaults'    => [
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes'  => [
                            'savvecentral' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/[savvecentral]',
                                    'constraints' => [
                                        'page' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'directory'
                                    ]
                                ]
                            ],
                            'paid' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/[paid]',
                                    'constraints' => [
                                        'page' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'paid-directory'
                                    ]
                                ]
                            ],
                            'trial' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/trial',
                                    'constraints' => [
                                        'page' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'trial-directory'
                                    ]
                                ]
                            ],
                            'trial-ended' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/ended-trial',
                                    'constraints' => [
                                        'page' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'ended-trial-directory'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'add-account' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/add-account',
                            'defaults' => [
                                'action' => 'add-account'
                            ]
                        ],
                    ],
                    'link-account' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/link-account/:customer_code',
                            'defaults' => [
                                'action' => 'link-account'
                            ]
                        ],
                    ],
                    'unlink-account' => [
                    	'type'    => 'Segment',
                    	'options' => [
                    		'route'    => '/unlink-account/:customer_code',
                    		'defaults' => [
                    			'action' => 'unlink-account'
                    		]
                    	],
                    ],
                    'update-account' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => '/update-account/:account_id',
                            'constraints' => [
                                'account_id' => '[0-9]{1,}'
                            ],
                            'defaults'    => [
                                'action' => 'update-account'
                            ]
                        ]
                    ],
                    'create-public-keys' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => '/create-public-keys/:account_id[/confirm/:confirm]',
                            'constraints' => [
                                'account_id' => '[0-9]{1,}'
                            ],
                            'defaults'    => [
                                'action' => 'create-public-keys'
                            ]
                        ]
                    ],
                    'activate-account' => [
                    	'type'    => 'Segment',
                    	'options' => [
                    		'route'       => '/activate-account/:account_id[/confirm/:confirm]',
                    		'constraints' => [
                    			'account_id' => '[0-9]{1,}'
                    		],
                    		'defaults'    => [
                    			'action' => 'activate-account'
                    		]
                    	]
                    ],
                    'deactivate-account' => [
                    	'type'    => 'Segment',
                    	'options' => [
                    		'route'       => '/deactivate-account/:account_id[/confirm/:confirm]',
                    		'constraints' => [
                    			'account_id' => '[0-9]{1,}'
                    		],
                    		'defaults'    => [
                    			'action' => 'deactivate-account'
                    		]
                    	]
                    ],
                    'manage-authors' => [
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route'       => '/manage-authors/:account_id',
                            'constraints' => [
                                'account_id' => '[0-9]{1,}'
                            ],
                            'defaults'    => [
                                'action' => 'directory',
                                'controller'=>'Elucidat\Controller\Author',
                            ]
                        ],
                        'child_routes'  => [
                            'create' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/create',
                                    'constraints' => [
                                        'page' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'create'
                                    ]
                                ]
                            ],
                            'link' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/link/:email',
                                    'constraints' => [

                                    ],
                                    'defaults'    => [
                                        'action' => 'link'
                                    ]
                                ]
                            ],
                            'delete' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/delete/:author_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'author_id' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'delete'
                                    ]
                                ]
                            ],
                            'activate' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/activate/:author_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'author_id' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'activate'
                                    ]
                                ]
                            ],
                            'deactivate' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => '/deactivate/:author_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'author_id' => '[0-9]+'
                                    ],
                                    'defaults'    => [
                                        'action' => 'deactivate'
                                    ]
                                ]
                            ],

                        ]
                    ]
                ]
            ]
        ]
    ]
];