<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Minute extends AbstractHelper
{
    /**
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     *
     * @return int|mixed
     */
    public function __invoke($dateStart, $dateEnd)
    {

        if (null != $dateEnd) {
            
            $dateStart = $dateStart->format('Y-m-d H:i:s');
            $dateEnd = $dateEnd->format('Y-m-d H:i:s');

            $start = new \DateTime($dateStart);
            $diff = $start->diff(new \DateTime($dateEnd));
            $minutes = $diff->days * 24 * 60;
            $minutes += $diff->h * 60;
            $minutes += $diff->i;

            return $minutes;

        } else {
            return 'n.d.';
        }
    }
}