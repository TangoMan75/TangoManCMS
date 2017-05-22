<?php

namespace AppBundle\Repository\Traits;

/**
 * Trait SearchableOrderedPaged
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait Exportable
{
    /**
     * Return all entities with joined author email (for export)
     * @return mixed
     */
    public function exportAllWithUserEmail()
    {
        return $this->createQueryBuilder($this->getTableName())
            ->leftJoin($this->getTableName().'.user', 'user')
            ->addSelect('user.email AS user_email')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * Return all entities (for export)
     * @return mixed
     */
    public function exportAll()
    {
        return $this->createQueryBuilder($this->getTableName())
            ->getQuery()
            ->getScalarResult();
    }
}
