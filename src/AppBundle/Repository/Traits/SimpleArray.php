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
            ->setParameter(':search', $search)
            ->orWhere($this->getTableName().'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :middle')
            ->setParameter(':middle', "%,$search,%");

        return $dql;
    }

    /**
     * @param QueryBuilder $dql
     * @param              $column
     * @param              $search
     *
     * @return QueryBuilder
     */
    public function findInSimpleArray(QueryBuilder $dql, $column, $search)
    {
            $dql->andWhere(':search IN '.$this->getTableName().'.'.$column)
                ->setParameter(':search', $search);

        return $dql;
    }
}
