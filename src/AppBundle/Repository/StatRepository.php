<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Class StatRepository
 *
 * @package AppBundle\Repository
 */
class StatRepository extends EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
