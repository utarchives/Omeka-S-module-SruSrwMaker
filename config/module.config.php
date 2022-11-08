<?php
namespace SruSrwMaker;

return [
    'api_adapters' => [
        'invokables' => [
            'srw_maps' => Api\Adapter\SrwMapAdapter::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            OMEKA_PATH.'/modules/SruSrwMaker/view',
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => OMEKA_PATH . '/modules/SpecialCharacterSearch/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
        ],
        'factories' => [
            'SruSrwMaker\Controller\Site\Index' => Service\Controller\Site\IndexControllerFactory::class,
            'SruSrwMaker\Controller\Admin\Index' => Service\Controller\Admin\IndexControllerFactory::class,
        ],
    ],
    'block_layouts' => [
        'invokables' => [
        ],
        'factories' => [

        ],
    ],
    'form_elements' => [
        'factories' => [
        ],
    ],
    'navigation_links' => [
        'invokables' => [
        ],
    ],
    'view_helpers' => [
        'invokables' => [
        ],
        'factories' => [
        ]
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Sru Srw Maker',
                'route' => 'admin/sru-srw-maker/map-import',
                'resource' => 'SruSrwMaker\Controller\Admin\Index',
                'controller' => 'Index',
                'action' => 'map-import',
                'pages' => [
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'site' => [
                'child_routes' => [
                    'sru' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/sru',
                            'defaults' => [
                                '__NAMESPACE__' => 'SruSrwMaker\Controller\Site',
                                'controller' => 'Index',
                                'action' => 'browse-sru',
                            ],
                        ],
                    ],
                    'srw' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/srw',
                            'defaults' => [
                                '__NAMESPACE__' => 'SruSrwMaker\Controller\Site',
                                'controller' => 'Index',
                                'action' => 'browse-srw',
                            ],
                        ],
                    ],
                    'wsdlsrw' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/wsdlsrw',
                            'defaults' => [
                                '__NAMESPACE__' => 'SruSrwMaker\Controller\Site',
                                'controller' => 'Index',
                                'action' => 'browse-wsdl',
                            ],
                        ],
                    ],
//                     'srwclient' => [
//                         'type' => 'Segment',
//                         'options' => [
//                             'route' => '/srwclient',
//                             'defaults' => [
//                                 '__NAMESPACE__' => 'SruSrwMaker\Controller\Site',
//                                 'controller' => 'Index',
//                                 'action' => 'browse-client',
//                             ],
//                         ],
//                     ],
                ],
            ],
            'admin' => [
                'child_routes' => [
                    'sru-srw-maker' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/sru-srw-maker',
                            'defaults' => [
                                '__NAMESPACE__' => 'SruSrwMaker\Controller\Admin',
                                'controller' => 'Index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'map-import' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/map-import',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'SruSrwMaker\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'map-import',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
