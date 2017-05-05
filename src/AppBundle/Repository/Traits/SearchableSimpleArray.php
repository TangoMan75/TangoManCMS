<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait SearchableSimpleArray
 *
 * @package AppBundle\Repository\Traits
 */
Trait SearchableSimpleArray
{
    /**
     * @param QueryBuilder $dql
     * @param              $column
     * @param              $search
     *
     * @return QueryBuilder
     */
    public function searchSimpleArray(QueryBuilder $dql, $column, $search)
    {
        $dql->andWhere($this->getTableName().'.'.$column.' LIKE :search')
            ->setParameter(':search', $search)
            ->orWhere($this->getTableName().'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :middle')
            ->setParameter(':middle', "%,$search,%");

        return $dql;
    }
}
