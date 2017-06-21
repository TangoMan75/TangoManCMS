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
    public function count($criteria = [])
    {
        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);

        $dql->select('COUNT('.$this->getTableName().')');

        return $dql->getQuery()->getSingleScalarResult();
    }
}
