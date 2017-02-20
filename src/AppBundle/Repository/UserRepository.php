<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * Gets all users by name paged
     *
     * @param int $page
     * @param int $max
     * @return Paginator
     */
    public function findByNamePaged($page = 1, $max = 10)
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

        $dql = $this->createQueryBuilder('user');
        $dql->orderBy('user.username', 'ASC');

        $firstResult = ($page - 1) * $max;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);
        $paginator = new Paginator($query);

        if( ($paginator->count() <=  $firstResult) && $page != 1 ) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * @param int $page
     * @param int $max
     * @param string $order
     * @param string $way
     * @return Paginator
     */
    public function sorting($page = 1, $max = 10, $order = 'username', $way = 'DESC')
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

        $dql = $this->createQueryBuilder('user');

        switch($order) {
            case "posts":
                $dql->addSelect('COUNT(post.id) as orderParam');
                $dql->leftJoin('user.posts', 'post');
                break;

            case "comments":
                $dql->addSelect('COUNT(comment.id) as orderParam');
                $dql->leftJoin('user.comments', 'comment');
                break;

            default:
                $dql->addSelect('user.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy('user.id');
        $dql->orderBy('orderParam', $way);

        $firstResult = ($page - 1) * $max;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);
        $paginator = new Paginator($query);

        if( ($paginator->count() <=  $firstResult) && $page != 1 ) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * @param string $username
     *
     * @return mixed
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
