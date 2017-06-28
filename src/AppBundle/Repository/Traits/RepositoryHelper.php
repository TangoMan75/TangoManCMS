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
            $this->tableName = $em->getClassMetadata($this->getEntityName())->getTableName();
        }

        return $this->tableName;
    }

    /**
     * Returns element count
     *
     * @return int $count
     */
    public function count($criteria = [])
    {
        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);

        $dql->select('COUNT('.$this->getTableName().')');

        return $dql->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $pages
     * @param int $limit
     *
     * @return Paginator
     */
    public function findAllPaged($page = 1, $limit = 10, $criteria = [])
    {
        if (!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if (!is_numeric($limit)) {
            throw new \InvalidArgumentException(
                '$limit must be an integer ('.gettype($limit).' : '.$limit.')'
            );
        }

        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);

        $firstResult = ($page - 1) * $limit;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($limit);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * Return all entities with joined author email (for export)
     *
     * @param ParameterBag $query
     * @param bool         $joinUserEmail
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

    /**
     * @param QueryBuilder $dql
     * @param              $column
     * @param              $search
     *
     * @return QueryBuilder
     */
    public function searchSimpleArray(QueryBuilder $dql, $column, $search)
    {
        $dql->andWhere($this->getTableName().'.'.$column.' LIKE :search')
            ->setParameter(':search', "%,$search,%")
            ->orWhere($this->getTableName().'.'.$column.' = :single')
            ->setParameter(':single', $search)
            ->orWhere($this->getTableName().'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search");

        return $dql;
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
