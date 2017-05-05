<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Class PrivilegeRepository
 *
 * @package AppBundle\Repository
 */
class PrivilegeRepository extends \Doctrine\ORM\EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
