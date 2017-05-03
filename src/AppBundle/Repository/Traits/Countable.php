<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

Trait Countable
{
    /**
     * Get count
     *
     * @return int $count
     */
    public function count($column)
    {
        return $this->createQueryBuilder($column)
            ->select('COUNT('.$column.')')
            ->getQuery()
            ->getSingleScalarResult();
    }
}