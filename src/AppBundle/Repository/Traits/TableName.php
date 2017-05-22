<?php

namespace AppBundle\Repository\Traits;

/**
 * Class AbstractRepository
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository
 */
Trait TableName
{
    /**
     * @var null
     */
    private $tableName = null;

    /**
     * @return string
     */
    public function getTableName()
    {
        if (!$this->tableName) {
            $entityName = $this->getEntityName();
            $em = $this->getEntityManager();
            $this->tableName = $em->getClassMetadata($entityName)->getTableName();
        }

        return $this->tableName;
    }
}
