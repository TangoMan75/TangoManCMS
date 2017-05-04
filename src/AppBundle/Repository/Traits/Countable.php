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
        return $this->createQueryBuilder($this->getName())
            ->select('COUNT('.$this->getName().')')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
