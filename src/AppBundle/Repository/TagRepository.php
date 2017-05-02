<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TagRepository extends EntityRepository
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function orderedSearchPaged(ParameterBag $query)
    {
        // Sets default values
        $page  = $query->get('page', 1);
        $limit = $query->get('limit', 20);
        $order = $query->get('order', 'name');
        $way   = $query->get('way', 'ASC');

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

        $dql = $this->createQueryBuilder('tag');

        // Search
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'articles':
                $dql->addSelect('COUNT(articles) as orderParam');
                $dql->leftJoin('tag.articles', 'articles');
                break;

            default:
                $dql->addSelect('tag.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy('tag.id');
        $dql->orderBy('orderParam', $way);

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
            $dql->andWhere('tag.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('name')) {
            $dql->andWhere('tag.name LIKE :name')
                ->setParameter(':name', '%'.$query->get('name').'%');
        }

        if ($query->get('type')) {
            $dql->andWhere('tag.type LIKE :type')
                ->setParameter(':type', '%'.$query->get('type').'%');
        }

        if ($query->get('label')) {
            $dql->andWhere('tag.label LIKE :label')
                ->setParameter(':label', '%'.$query->get('label').'%');
        }

        return $dql;
    }

    /**
     * Get tag count
     * @return int $count tag count
     */
    public function count()
    {
        return $this->createQueryBuilder('tag')
            ->select('COUNT(tag)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
