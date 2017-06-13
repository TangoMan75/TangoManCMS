<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait FindByQuery
 * Requires repository to own "TableName" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository\Traits
 */
Trait FindByQuery
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function findByQuery(ParameterBag $query, $criteria = array())
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 10);

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

        // Merge criteria with query
        $query->replace(array_merge($query->all(), $criteria));
        // QueryBuilder
        $dql = $this->createQueryBuilder($this->getTableName());
        // Search
        $dql = $this->search($dql, $query);
        // Order
        $dql = $this->order($dql, $query);

        $firstResult = ($page - 1) * $limit;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($limit);
        $paginator = new Paginator($query);

        try {
            if (($paginator->count() <= $firstResult) && $page != 1) {
                throw new NotFoundHttpException('Page not found');
            }
        } catch (QueryException $qe) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }
}
