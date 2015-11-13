<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Config extends AbstractHelper {

    protected $configService;

    public function __construct($configService) {
        $this->configService = $configService;
    }

    public function __invoke() {
        $args = func_get_args();

        $ret = $this->configService;

        foreach ($args as $arg) {
            if (isset($ret[$arg])) {
                $ret = $ret[$arg];
            } else {
                return false;
            }
        }

        // return only values, not arrays
        if (is_array($ret)) {
            return false;
        }

        return $ret;
    }
}
