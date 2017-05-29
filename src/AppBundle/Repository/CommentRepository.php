<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Post;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;

/**
 * Class CommentRepository
 *
 * @package AppBundle\Repository
 */
class CommentRepository extends EntityRepository
{
    use Traits\Countable;
    use Traits\Ordered;
    use Traits\Searchable;
    use Traits\SearchableSimpleArray;
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
        $dql = $this->createQueryBuilder('comment');
        // Search
        $dql = $this->search($dql, $query);
        // Order
        $dql = $this->order($dql, $query);
        // Joins User
        $dql->leftJoin('comment.user', 'user');
        // Group
        $dql->groupBy('comment.id');

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
            $dql->andWhere('comment.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('post')) {
            $dql->andWhere('post.title LIKE :post')
                ->leftJoin('comment.post', 'post')
                ->setParameter(':post', '%'.$query->get('post').'%');
        }

        switch ($query->get('published')) {
            case 'true':
                $dql->andWhere('comment.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere('comment.published = :published')
                    ->setParameter(':published', 0);
        }

        if ($query->get('text')) {
            $dql->andWhere('comment.content LIKE :content')
                ->setParameter(':content', '%'.$query->get('text').'%');
        }

        if ($query->get('user')) {
            $dql->andWhere('user.username LIKE :user')
                ->leftJoin('comment.user', 'user')
                ->setParameter(':user', '%'.$query->get('user').'%');
        }

        return $dql;
    }

    /**
     * Comments pagination.
     *
     * @param Post $post
     * @param int  $page
     * @param int  $max
     *
     * @return Paginator
     */
    public function findAllPaged(Post $post, $page = 1, $max = 10)
    {
        if (!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if (!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$max must be an integer ('.gettype($max).' : '.$max.')'
            );
        }

        // Queries post comments
        $dql = $this->createQueryBuilder('comment');
        $dql->andWhere('comment.post = :post')
            ->setParameter(':post', $post)
            ->orderBy('comment.created', 'DESC');

        $firstResult = ($page - 1) * $max;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }
}