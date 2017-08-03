<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PostRepository
 *
 * @package AppBundle\Repository
 */
class PostRepository extends EntityRepository
{
    use Traits\RepositoryHelper;
}
