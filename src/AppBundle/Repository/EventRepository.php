<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Class EventRepository
 *
 * @package AppBundle\Repository
 */
class EventRepository extends AbstractRepository {
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
}
