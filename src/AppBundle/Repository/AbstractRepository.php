<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository {
	
	private $tableName = null;

	protected function getTableName() {
		if ($this->tableName === null) {
	        $entityName = $this->getEntityName();
	        $em = $this->getEntityManager();
	        $this->tableName = $em->getClassMetadata($entityName)->getTableName();
		}
		return $this->tableName;		
    }

}