<?php

namespace AppBundle\Repository\Traits;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait OrderedSearch
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait OrderedSearch
{
    /**
     * @param ParameterBag $query
     */
    public function orderedSearch(ParameterBag $query)
    {
        // QueryBuilder
        $dql = $this->createQueryBuilder($this->getTableName());

        // Search
        $dql = $this->search($dql, $query);

        // Order
        $dql = $this->order($dql, $query);

        return $dql
            ->getQuery()
            ->getScalarResult();
    }
}
