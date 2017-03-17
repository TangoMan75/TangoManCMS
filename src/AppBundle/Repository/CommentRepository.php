<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Post;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentRepository extends EntityRepository
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

        $dql = $this->createQueryBuilder('comment');

        // Search
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'user':
                $dql->addSelect('user.username as orderParam');
                $dql->leftJoin('comment.user', 'user');
                break;

            case 'post':
                $dql->addSelect('post.title as orderParam');
                $dql->leftJoin('comment.post', 'post');
                break;

            default:
                $dql->addSelect('comment.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy('comment.id');
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
        if ($query->get('s_id')) {
            $dql->andWhere('comment.id = :id')
                ->setParameter(':id', $query->get('s_id'));
        }

        if ($query->get('s_post')) {
            $dql->andWhere('post.name LIKE :post')
                ->leftJoin('comment.posts', 'post')
                ->setParameter(':post', '%'.$query->get('s_post').'%');
        }

        if ($query->get('s_user')) {
            $dql->andWhere('user.username LIKE :user')
                ->leftJoin('comment.user', 'user')
                ->setParameter(':user', '%'.$query->get('s_user').'%');
        }

        if ($query->get('s_content')) {
            $dql->andWhere('comment.content LIKE :content')
                ->setParameter(':content', '%'.$query->get('s_content').'%');
        }

        switch ($query->get('s_published')) {
            case 'true':
                $dql->andWhere('comment.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere('comment.published = :published')
                    ->setParameter(':published', 0);
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

    /**
     * Get comment count
     *
     * @return int $count comment count
     */
    public function count()
    {
        return $this->createQueryBuilder('comment')
            ->select('COUNT(comment)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
