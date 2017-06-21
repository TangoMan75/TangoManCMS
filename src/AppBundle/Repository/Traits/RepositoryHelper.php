<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository
 */
Trait RepositoryHelper
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

    /**
     * @param QueryBuilder $dql
     * @param array        $criteria
     *
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $dql, $criteria = [])
    {
        $index = 0;
        foreach ($criteria as $param => $value) {
            if (is_array($value)) {
                $dql->andWhere($this->getTableName().'.'.$param.' IN(:filterParam_'.$index.')')
                    ->setParameter(':filterParam_'.$index, array_values($value));
            } else {
                $dql->andWhere($this->getTableName().'.'.$param.' = :filterParam_'.$index)
                    ->setParameter(':filterParam_'.$index, $value);
            }
            $index++;
        }

        return $dql;
    }
}
