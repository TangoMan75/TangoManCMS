<?php

namespace AppBundle\Repository\Traits;

Trait TableName
{
    /**
     * @return bool|string
     */
    public function getTableName()
    {
        $entity = strrchr($this->getEntityName(), '\\');
        $entity = substr($entity, 1, strlen($entity)-1);

        return mb_strtolower($entity);
    }
}
