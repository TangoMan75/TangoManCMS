<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostRepository extends EntityRepository
{
    /**
     * Posts pagination
     *
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findAllPaged($page = 1, $max = 10)
    {
        if( !is_numeric($page) ) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if( !is_numeric($page) ) {
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

        if( ($paginator->count() <= $firstResult) && $page != 1 ) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * Post pagination by user
     *
     * @param User $user
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findByUserPaged(User $user, $page = 1, $max = 10)
    {
        if( !is_numeric($page) ) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if( !is_numeric($page) ) {
            throw new \InvalidArgumentException(
                '$max must be an integer ('.gettype($max).' : '.$max.')'
            );
        }

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->andWhere('post.user = :user');
        $dql->setParameter(':user', $user);
        $dql->orderBy('post.dateCreated', 'DESC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if( ($paginator->count() <= $firstResult) && $page != 1 ) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * Post pagination by username
     *
     * @param string $username
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findByUsernamePaged($username, $page = 1, $max = 10)
    {
        if( !is_numeric($page) ) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }
        if( !is_numeric($page) ) {

            throw new \InvalidArgumentException(
                '$max must be an integer ('.gettype($max).' : '.$max.')'
            );
        }

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->join('post.user', 'user');
        $dql->andWhere('user.username = :username');
        $dql->setParameter(':username', $username);
        $dql->orderBy('post.dateCreated', 'DESC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if( ($paginator->count() <= $firstResult) && $page != 1 ) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }
}
