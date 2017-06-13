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
    use Traits\Ordered;
    use Traits\Parse;
    use Traits\Searchable;
    use Traits\SimpleArray;
    use Traits\TableName;
}
