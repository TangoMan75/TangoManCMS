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
    use Traits\Countable;
    use Traits\FindByQuery;
    use Traits\Ordered;
    use Traits\Parse;
    use Traits\Searchable;
    use Traits\SimpleArray;
    use Traits\TableName;
}
