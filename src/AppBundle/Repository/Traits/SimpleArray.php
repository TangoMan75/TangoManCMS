<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait SimpleArray
 *
 * @package AppBundle\Repository\Traits
 */
Trait SimpleArray
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
            ->setParameter(':search', "%,$search,%")
            ->orWhere($this->getTableName().'.'.$column.' = :single')
            ->setParameter(':single', $search)
            ->orWhere($this->getTableName().'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search");

        return $dql;
    }
}
