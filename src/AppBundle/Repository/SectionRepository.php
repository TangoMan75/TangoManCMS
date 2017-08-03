<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SectionRepository
 *
 * @package AppBundle\Repository
 */
class SectionRepository extends EntityRepository
{
    use Traits\RepositoryHelper;
}
