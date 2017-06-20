<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait Countable
 * Requires repository to own "TableName" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository\Traits
 */
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
