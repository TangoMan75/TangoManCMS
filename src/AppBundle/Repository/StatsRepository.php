<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Class PrivilegeRepository
 *
 * @package AppBundle\Repository
 */
class StatsRepository extends EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
