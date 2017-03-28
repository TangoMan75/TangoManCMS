<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageRepository extends EntityRepository
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function sortedSearchPaged(ParameterBag $query)
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 20);
        $order = $query->get('order', 'modified');
        $way = $query->get('way', 'DESC');

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

        $dql = $this->createQueryBuilder('page');

        // Search
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'items':
                $dql->addSelect('COUNT(items) as orderParam');
                $dql->leftJoin('page.items', 'items');
                break;

            case 'tags':
                $dql->addSelect('COUNT(ctags) as orderParam');
                $dql->leftJoin('page.tags', 'ctags');
                break;

            default:
                $dql->addSelect('page.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy('page.id');
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
            $dql->andWhere('page.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('slug')) {
            $dql->andWhere('page.slug LIKE :slug')
                ->setParameter(':slug', '%'.$query->get('slug').'%');
        }

        if ($query->get('title')) {
            $dql->andWhere('page.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('title').'%');
        }

        switch ($query->get('published')) {
            case 'true':
                $dql->andWhere('page.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere('page.published = :published')
                    ->setParameter(':published', 0);
        }

        if ($query->get('tag')) {
            $dql->andWhere('tag.name LIKE :tag')
                ->leftJoin('page.tags', 'tag')
                ->setParameter(':tag', '%'.$query->get('tag').'%');
        }

        return $dql;
    }

    /**
     * Get page count
     *
     * @return int $count page count
     */
    public function count()
    {
        return $this->createQueryBuilder('page')
            ->select('COUNT(page)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
