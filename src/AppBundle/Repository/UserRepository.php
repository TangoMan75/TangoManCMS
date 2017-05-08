<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserRepository
 *
 * @package AppBundle\Repository
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;

    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function orderedSearchPaged(ParameterBag $query)
    {
        // Sets default values
        $page  = $query->get('page', 1);
        $limit = $query->get('limit', 20);
        $order = $query->get('order', 'username');
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

        $dql = $this->createQueryBuilder('user');

        // Search inside simple arrays
        if ($query->get('role')) {
            $dql = $this->searchSimpleArray($dql, 'roles', $query->get('role'));
        }

        // Search inside id, username and email columns
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'comments':
                $dql->addSelect('COUNT(comment.id) as orderParam');
                $dql->leftJoin('user.comments', 'comment');
                break;

            case 'posts':
                $dql->addSelect('COUNT(post.id) as orderParam');
                $dql->leftJoin('user.posts', 'post');
                break;

            case 'password':
                $dql->addSelect('COUNT(user.password) as orderParam');
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
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        if ($query->get('email')) {
            $dql->andWhere('user.email LIKE :email')
                ->setParameter(':email', '%'.$query->get('email').'%');
        }

        if ($query->get('id')) {
            $dql->andWhere('user.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        switch ($query->get('password')) {
            case 'true':
                $dql->andWhere('user.password IS NOT NULL');
                break;
            case 'false':
                $dql->andWhere('user.password IS NULL');
        }

        if ($query->get('username')) {
            $dql->andWhere('user.username LIKE :username')
                ->setParameter(':username', '%'.$query->get('username').'%');
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
