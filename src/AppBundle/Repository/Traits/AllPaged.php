<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait AllPaged
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait AllPaged
{
    /**
     * @param int $pages
     * @param int $limit
     *
     * @return Paginator
     */
    public function findAllPaged($page = 1, $limit = 10, $published = true)
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

        if ($published) {
            $dql->andWhere($this->getTableName().'.published = 1');
        }

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
}
