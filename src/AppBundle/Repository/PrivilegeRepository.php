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
    use Traits\FindByQuery;
    use Traits\SimpleArray;
    use Traits\TableName;
}
