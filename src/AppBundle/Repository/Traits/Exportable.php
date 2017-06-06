<?php

namespace AppBundle\Repository\Traits;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait Exportable
 * Requires repository to own "TableName" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository\Traits
 */
Trait Exportable
{
    /**
     * Return all entities with joined author email (for export)
     *
     * @return mixed
     */
    public function export(ParameterBag $query, $joinUserEmail = false)
    {
        // QueryBuilder
        $dql = $this->createQueryBuilder($this->getTableName());
        // Search
        $dql = $this->search($dql, $query);
        // Order
        $dql = $this->order($dql, $query);

        if ($joinUserEmail) {
            $dql->leftJoin($this->getTableName().'.user', 'j_user')
                ->addSelect('j_user.email AS user_email');
        }

        return $dql->getQuery()->getScalarResult();
    }
}
