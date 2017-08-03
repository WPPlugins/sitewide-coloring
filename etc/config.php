<?php

return [
    'pluginDir' => dirname(__DIR__),
    'pluginUrl' => plugin_dir_url(realpath(__DIR__ . '/../sitewide-coloring.php')),
    'autoload' => [
        'namespaces' => [
            'SiteWideColoring\Hook' => dirname(__DIR__) . '/hooks',
            'SiteWideColoring\Service' => dirname(__DIR__) . '/services',
            'SiteWideColoring\App' => dirname(__DIR__) . '/app',
            'Detection' => dirname(__DIR__) . '/lib/MobileDetect/namespaced/Detection',
        ],
    ],
    'registry' => [
        'detection' => 'Detection\MobileDetect',
        'hook/admin/settings' => 'SiteWideColoring\Hook\Admin\Settings',
        'hook/sitewiding' => 'SiteWideColoring\Hook\SiteWiding',
        'service/settings' => 'SiteWideColoring\Service\Settings',
        'service/sitewiding' => 'SiteWideColoring\Service\SiteWiding'
    ],
    'layout' => [
        'styles' => [
            'sitewide' => 'assets/styles/sitewide.css'
        ]
    ],
    'filters' => [
        'the_content' => [
            0 => [
                'hook' => 'hook/sitewiding',
                'action' => 'injectAction',
                'priority' => 10
            ],
            1 => [
                'function' => 'do_shortcode',
                'priority' => 11
            ],
            2 => [
                'hook' => 'hook/sitewiding',
                'action' => 'filterContentAction'
            ]
        ]
    ],
    'admin' => [
        'actions' => [
            'admin_menu' => [
                'hook' => 'hook/admin/settings',
                'action' => 'initializeAction'
            ]
        ],
        'layout' => [
            'scripts' => [
                'lib-jquery-micro-tpl' => 'assets/libs/jquery.micro-tpl/jquery.micro-tpl.min.js',
                'sitewide-ui' => 'assets/scripts/sitewide.js',
            ],
            'styles' => [
                'roboto-font-regular-latin' => '//fonts.googleapis.com/css?family=Roboto:300,400:latin',
                'roboto-condensed-font-regular-latin' => '//fonts.googleapis.com/css?family=Roboto+Condensed:400,300:latin',
                'sitewide-ui-styles' => 'assets/styles/sitewide.css'
            ]
        ]
    ]
];
