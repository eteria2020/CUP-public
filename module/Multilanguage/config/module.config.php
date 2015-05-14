<?php

namespace Multilanguage;

return [
    'service_manager' => [
        'factories' => [
            'LanguageService' => 'Multilanguage\Service\LanguageServiceFactory',
            'DetectLocaleService' => 'Multilanguage\Service\DetectLocaleServiceFactory'
        ]
    ],
    'translator' => [
        'locale' => 'it_IT',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
            [
                'type' => 'phparray',
                'base_dir'    => __DIR__ . '/../language/validators',
                'pattern'     => '%s.php',
                'text_domain' => 'default'
            ]
        ],
    ],
];
