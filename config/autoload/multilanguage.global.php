<?php

return [
    'multilanguage' => [
        'allowed_locales' => [
            'it_IT'
        ],
        // language ranges that are allowed in the application.
        // the keys must correspond to the value used in the pattern key in the
        // transaltor/translation_file_patterns configuration key
        'allowed_languages' => [
            'it_IT',
            'en_US',
            'sk-SK',
            'nl_NL',
        ],
        // listeners used to determine the appropriate language range for the
        // application.
        // The order is important!
        'listeners' => [
            'LanguageFromSessionDetectorListener',
            'FilterByConfigurationDetectorListener',
            'ReturnFirstValueDetectorListener'
        ]
    ]
];
