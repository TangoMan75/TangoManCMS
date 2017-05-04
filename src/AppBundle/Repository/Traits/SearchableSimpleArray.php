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
    public function searchSimpleArray(QueryBuilder $dql, $column, $search)
    {
        $dql->andWhere($this->getName().'.'.$column.' LIKE :search')
            ->setParameter(':search', $search)
            ->orWhere($this->getName().'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($this->getName().'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search")
            ->orWhere($this->getName().'.'.$column.' LIKE :middle')
            ->setParameter(':middle', "%,$search,%");

        return $dql;
    }
}
