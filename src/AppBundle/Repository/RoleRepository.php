<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RoleRepository
 *
 * @package AppBundle\Repository
 */
class RoleRepository extends EntityRepository
{
    use Traits\Countable;
    use Traits\Ordered;
    use Traits\Searchable;
    use Traits\TableName;

    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function searchableOrderedPage(ParameterBag $query)
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 20);

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

        // QueryBuilder
        $dql = $this->createQueryBuilder('role');
        // Search
        $dql = $this->search($dql, $query);
        // Order
        $dql = $this->order($dql, $query);
        // Group
        $dql->groupBy('role.id');

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
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        if ($query->get('id')) {
            $dql->andWhere('role.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('name')) {
            $dql->andWhere('role.name LIKE :name')
                ->setParameter(':name', '%'.$query->get('name').'%');
        }

        if ($query->get('type')) {
            $dql->andWhere('role.type LIKE :type')
                ->setParameter(':type', '%'.$query->get('type').'%');
        }

        return $dql;
    }
}
