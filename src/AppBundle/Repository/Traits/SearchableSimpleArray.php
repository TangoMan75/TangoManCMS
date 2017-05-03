<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

Trait SearchableSimpleArray
{
    /**
     * @param QueryBuilder $dql
     * @param              $column
     * @param              $search
     *
     * @return QueryBuilder
     */
    public function searchSimpleArray(QueryBuilder $dql, $table, $column, $search)
    {
        $dql->andWhere($table.'.'.$column.' LIKE :search')
            ->setParameter(':search', $search)
            ->orWhere($table.'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($table.'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search")
            ->orWhere($table.'.'.$column.' LIKE :middle')
            ->setParameter(':middle', "%,$search,%");

        return $dql;
    }
}
