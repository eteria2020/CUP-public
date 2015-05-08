<?php

namespace Multilanguage;

return [
    'service_manager' => [
        'factories' => [
            'LanguageService' => 'Multilanguage\Service\LanguageServiceFactory',
            'DetectLocaleService' => 'Multilanguage\Service\DetectLocaleServiceFactory'
        ]
    ]
];
