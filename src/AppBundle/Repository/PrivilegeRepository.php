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
    use Traits\Ordered;
    use Traits\Parse;
    use Traits\Searchable;
    use Traits\SimpleArray;
    use Traits\TableName;
}
