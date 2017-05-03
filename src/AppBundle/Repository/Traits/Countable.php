<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

Trait Countable
{
    /**
     * Get count
     *
     * @param $table
     *
     * @return int $count
     */
    public function count($table)
    {
        $name = \ReflectionClass::getName;

        return $this->createQueryBuilder($table)
            ->select('COUNT('.$table.')')
            ->getQuery()
            ->getSingleScalarResult();
    }
}