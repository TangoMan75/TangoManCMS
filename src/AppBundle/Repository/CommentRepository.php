<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Comments pagination
     *
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findByPage($page = 1, $max = 10)
    {
        if(!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if(!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$max must be an integer ('.gettype($max).' : '.$max.')'
            );
        }

        $dql = $this->createQueryBuilder('comment');
        $dql->orderBy('comment.dateCreated', 'DESC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if(($paginator->count() <=  $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

}
