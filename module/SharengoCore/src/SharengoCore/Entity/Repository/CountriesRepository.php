<?php

namespace SharengoCore\Entity\Repository;

/**
 * CountriesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountriesRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllCountries()
    {
        $countries = $this->createQueryBuilder('c')
            ->select('c.code, c.name')
            ->orderBy('c.code')
            ->getQuery();

        return $countries->getResult();
    }
}
