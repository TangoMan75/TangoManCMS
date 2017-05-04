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
    public function count()
    {
        return $this->createQueryBuilder($this->getTableName())
            ->select('COUNT('.$this->getTableName().')')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
