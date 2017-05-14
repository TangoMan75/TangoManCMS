<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Class EventRepository
 *
 * @package AppBundle\Repository
 */
class EventRepository extends \Doctrine\ORM\EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
