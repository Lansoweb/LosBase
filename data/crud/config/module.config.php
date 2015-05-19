<?php
return [
    'controllers' => [
        'invokables' => [
            '__MODULENAME__\Controller\Crud' => '__MODULENAME__\Controller\CrudController',
        ],
    ],
    'router' => [
        'routes' => [
            '__MODULEDASHEDNAME__' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/__MODULEDASHEDNAME__',
                    'defaults' => [
                        'controller' => '__MODULENAME__\Controller\Crud',
                        'action' => 'list',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'list' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/list',
                            'defaults' => [
                                'controller' => '__MODULENAME__\Controller\Crud',
                                'action' => 'list',
                            ],
                        ],
                    ],
                    'view' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/view[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => '__MODULENAME__\Controller\Crud',
                                'action' => 'view',
                                'id' => 0,
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'controller' => '__MODULENAME__\Controller\Crud',
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/edit[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => '__MODULENAME__\Controller\Crud',
                                'action' => 'edit',
                                'id' => 0,
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/delete[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => '__MODULENAME__\Controller\Crud',
                                'action' => 'delete',
                                'id' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            '__MODULENAME__' => __DIR__.'/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            '__MODULENAME___driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__.'/../src/__MODULENAME__/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    '__MODULENAME__\Entity' => '__MODULENAME___driver',
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            'Crud' => [
                'pages' => [
                    '__MODULEDASHEDNAME__' => [
                        'label' => '__MODULENAME__',
                        'route' => '__MODULEDASHEDNAME__/list',
                        'pages' => [
                            'list' => [
                                'label' => 'List',
                                'route' => '__MODULEDASHEDNAME__/list',
                            ],
                            'view' => [
                                'label' => 'View',
                                'route' => '__MODULEDASHEDNAME__/view',
                            ],
                            'add' => [
                                'label' => 'Add',
                                'route' => '__MODULEDASHEDNAME__/add',
                            ],
                            'edit' => [
                                'label' => 'Edit',
                                'route' => '__MODULEDASHEDNAME__/edit',
                            ],
                            'delete' => [
                                'label' => 'Delete',
                                'route' => '__MODULEDASHEDNAME__/delete',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]
];
