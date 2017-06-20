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
            $em = $this->getEntityManager();
//            $this->tableName = $em->getClassMetadata(get_class($this))->getTableName();
            $this->tableName = $em->getClassMetadata($this->getEntityName())->getTableName();
        }

        return $this->tableName;
    }
}
