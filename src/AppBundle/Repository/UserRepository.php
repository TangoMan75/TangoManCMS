<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class UserRepository
 *
 * @package AppBundle\Repository
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{

    use RepositoryHelper;

    /**
     * Required for user login
     *
     * @param string $usernameOrEmail
     *
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($usernameOrEmail)
    {
        return $this->createQueryBuilder('user')
                    ->where('user.username = :username OR user.email = :email')
                    ->setParameter('username', $usernameOrEmail)
                    ->setParameter('email', $usernameOrEmail)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
