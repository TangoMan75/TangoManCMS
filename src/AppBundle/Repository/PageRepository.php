<?php

namespace AppBundle\Repository;

class PageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get page count
     *
     * @return int $count page count
     */
    public function count()
    {
        return $this->createQueryBuilder('page')
            ->select('COUNT(page)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
