<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function sortedSearchPaged(ParameterBag $query, $limit = 10)
    {
        // Sets default values
        $page = $query->get('page', 1);
        $order = $query->get('order', 'username');
        $way = $query->get('way', 'ASC');

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

        $dql = $this->createQueryBuilder('user');

        // Search inside simple arrays
        if ($query->get('s_role')) {
            $dql = $this->searchSimpleArray($dql, 'roles', $query->get('s_role'));
        }

        // Search inside id, username and email columns
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'posts':
                $dql->addSelect('COUNT(post.id) as orderParam');
                $dql->leftJoin('user.posts', 'post');
                break;

            case 'comments':
                $dql->addSelect('COUNT(comment.id) as orderParam');
                $dql->leftJoin('user.comments', 'comment');
                break;

            default:
                $dql->addSelect('user.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy('user.id');
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
     * Get user count
     *
     * @return int $count user count
     */
    public function count()
    {
        return $this->createQueryBuilder('user')
            ->select('COUNT(user)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * Gets all users by name paged
//     *
//     * @param int $page
//     * @param int $limit
//     *
//     * @return Paginator
//     */
//    public function findByNamePaged($page = 1, $limit = 10)
//    {
//        if (!is_numeric($page)) {
//            throw new \InvalidArgumentException(
//                '$page must be an integer ('.gettype($page).' : '.$page.')'
//            );
//        }
//
//        if (!is_numeric($page)) {
//            throw new \InvalidArgumentException(
//                '$limit must be an integer ('.gettype($limit).' : '.$limit.')'
//            );
//        }
//
//        $dql = $this->createQueryBuilder('user');
//
//        $dql->orderBy('user.username', 'ASC');
//
//        $firstResult = ($page - 1) * $limit;
//        $query = $dql->getQuery();
//        $query->setFirstResult($firstResult);
//        $query->setMaxResults($limit);
//        $paginator = new Paginator($query);
//
//        if (($paginator->count() <= $firstResult) && $page != 1) {
//            throw new NotFoundHttpException('Page not found');
//        }
//
//        return $paginator;
//    }

    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        if ($query->get('s_id')) {
            $dql->andWhere('user.id = :id')
                ->setParameter(':id', $query->get('s_id'));
        }

        if ($query->get('s_username')) {
            $dql->andWhere('user.username LIKE :username')
                ->setParameter(':username', '%'.$query->get('s_username').'%');
        }

        if ($query->get('s_email')) {
            $dql->andWhere('user.email LIKE :email')
                ->setParameter(':email', '%'.$query->get('s_email').'%');
        }

        return $dql;
    }

    /**
     * @param  string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        $dql = $this->createQueryBuilder('user');
        $dql = $this->searchSimpleArray($dql, 'roles', $role);

        return $dql->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $dql
     * @param              $columnName
     * @param              $search
     *
     * @return QueryBuilder
     */
    public function searchSimpleArray(QueryBuilder $dql, $columnName, $search)
    {
        $dql->andWhere('user.'.$columnName.' LIKE :search')
            ->setParameter(':search', $search)
            ->orWhere('user.'.$columnName.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere('user.'.$columnName.' LIKE :end')
            ->setParameter(':end', "%,$search")
            ->orWhere('user.'.$columnName.' LIKE :middle')
            ->setParameter(':middle', "%,$search,%");

        return $dql;
    }

    /**
     * Required for user login
     *
     * @param string $username
     *
     * @return mixed
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('user')
            ->where('user.username = :username OR user.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
