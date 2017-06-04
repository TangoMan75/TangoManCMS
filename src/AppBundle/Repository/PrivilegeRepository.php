<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Class PrivilegeRepository
 *
 * @package AppBundle\Repository
 */
class PrivilegeRepository extends EntityRepository
{
    use Traits\Countable;
    use Traits\Ordered;
    use Traits\Searchable;
    use Traits\SearchableOrderedPaged;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
