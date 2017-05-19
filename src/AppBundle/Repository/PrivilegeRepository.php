<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Class PrivilegeRepository
 *
 * @package AppBundle\Repository
 */
class PrivilegeRepository extends AbstractRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
}
