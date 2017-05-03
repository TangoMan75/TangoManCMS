<?php

namespace AppBundle\Repository;

class RoleRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get role count
     *
     * @return int $count role count
     */
    public function count()
    {
        return $this->createQueryBuilder('role')
            ->select('COUNT(role)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
