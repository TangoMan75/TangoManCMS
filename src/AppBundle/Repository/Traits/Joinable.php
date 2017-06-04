<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait Joinable
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait Joinable
{
    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function join(QueryBuilder $dql, ParameterBag $query)
    {
        $join = $query->get('join', 'user');

        switch ($join) {
            // Join user
            case 'user':
                $dql->leftJoin($this->getTableName().'.user', 'user');
                break;

            // Join post
            case 'post':
                $dql->leftJoin($this->getTableName().'.post', 'post');
                break;
        }

        return $dql;
    }
}
