<?php
/**
 * Created by PhpStorm.
 * User: MORIN Matthias
 * Date: 21/09/2016
 * Time: 17:53
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PostRepository extends EntityRepository
{
    public function idSuperior($id)
    {
        if(!is_numeric($id)){
            return false;
        }

        $dql = $this->createQueryBuilder('post');

        $dql->andWhere('post.id > :id');
        $dql->setParameter(':id', $id);

        return $dql->getQuery()->getResult();
    }

    /**
     * Posts pagination
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

        $dql = $this->createQueryBuilder('post');
        $dql->orderBy('post.dateCreated', 'DESC');

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

    /**
     * Post pagination by user
     *
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findByUser($user, $page = 1, $max = 10)
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

        $dql = $this->createQueryBuilder('post');
        $dql->where('post.userId', 'DESC');
        $dql->orderBy('post.dateCreated', 'DESC');

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
