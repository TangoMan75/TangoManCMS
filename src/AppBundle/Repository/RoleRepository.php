<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RoleRepository
 *
 * @package AppBundle\Repository
 */
class RoleRepository extends EntityRepository
{
    use Traits\RepositoryHelper;
}
