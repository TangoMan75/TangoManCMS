<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait OrderBy
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait OrderBy
{
    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function orderBy(QueryBuilder $dql, ParameterBag $query)
    {
        $orderby = $query->get('orderby', 'id');
        $way = $query->get('way', 'DESC');

        switch ($orderby) {
            // orderBy created
            case 'created':
                $dql->orderBy($this->getTableName().'.created', $way);
                break;

            // orderBy id
            case 'id':
                $dql->orderBy($this->getTableName().'.id', $way);
                break;

            // orderBy modified
            case 'modified':
                $dql->orderBy($this->getTableName().'.modified', $way);
                break;
        }

        $dql->orderBy('orderParam', $way);

        return $dql;
    }
}
