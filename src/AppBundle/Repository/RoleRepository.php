<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Class RoleRepository
 *
 * @package AppBundle\Repository
 */
class RoleRepository extends \Doctrine\ORM\EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
