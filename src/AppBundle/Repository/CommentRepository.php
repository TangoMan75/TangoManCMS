<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Post;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;

/**
 * Class CommentRepository
 *
 * @package AppBundle\Repository
 */
class CommentRepository extends EntityRepository
{
    use Traits\AllPaged;
    use Traits\Countable;
    use Traits\FindByQuery;
    use Traits\RepositoryHelper;
    use Traits\SimpleArray;
}
