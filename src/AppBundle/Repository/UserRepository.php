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
    use Traits\RepositoryHelper;

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
