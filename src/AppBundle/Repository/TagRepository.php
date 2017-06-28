<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TagRepository
 *
 * @package AppBundle\Repository
 */
class TagRepository extends EntityRepository
{
    use Traits\FindByQuery;
    use Traits\RepositoryHelper;
}
