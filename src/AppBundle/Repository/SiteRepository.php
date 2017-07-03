<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SiteRepository
 *
 * @package AppBundle\Repository
 */
class SiteRepository extends EntityRepository
{
    use Traits\FindByQuery;
    use Traits\RepositoryHelper;
}
