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
        $dql = $this->createQueryBuilder('user');
//        // Search inside simple arrays
//        if ($query->get('role')) {
//            $dql = $this->searchSimpleArray($dql, 'roles', $query->get('role'));
//        }
        // Search
        $dql = $this->search($dql, $query);
        // Order
        $dql = $this->order($dql, $query);
        // Group
        $dql->groupBy('user.id');

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
