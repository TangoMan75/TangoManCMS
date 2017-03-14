<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionRepository extends EntityRepository
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function sortedSearchPaged(ParameterBag $query)
    {
        // Sets default values
        $page  = $query->get('page', 1);
        $limit = $query->get('limit', 20);
        $order = $query->get('order', 'title');
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

        $dql = $this->createQueryBuilder('section');

        // Search
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'pages':
                $dql->addSelect('COUNT(page) as orderParam');
                $dql->leftJoin('section.pages', 'page');
                break;

            default:
                $dql->addSelect('section.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy('section.id');
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
     * Get page count
     * @return int $count page count
     */
    public function count()
    {
        return $this->createQueryBuilder('section')
            ->select('COUNT(section)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        if ($query->get('s_id')) {
            $dql->andWhere('page.id = :id')
                ->setParameter(':id', $query->get('s_id'));
        }

        if ($query->get('s_title')) {
            $dql->andWhere('page.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('s_title').'%');
        }

        if ($query->get('s_published')) {
            $dql->andWhere('page.published = :published')
                ->setParameter(':published', $query->get('s_published'));
        }

        if ($query->get('s_tag')) {
            $dql->andWhere('tag.name LIKE :tag')
                ->leftJoin('page.tags', 'tag')
                ->setParameter(':tag', '%'.$query->get('s_tag').'%');
        }

        return $dql;
    }
}
