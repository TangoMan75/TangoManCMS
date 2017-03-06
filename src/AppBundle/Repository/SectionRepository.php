<?php

namespace AppBundle\Repository;

class SectionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get section count
     *
     * @return int $count section count
     */
    public function count()
    {
        return $this->createQueryBuilder('section')
            ->select('COUNT(section)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
