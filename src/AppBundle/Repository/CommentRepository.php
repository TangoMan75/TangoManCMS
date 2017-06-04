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
    use Traits\SearchableOrderedPaged;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;

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