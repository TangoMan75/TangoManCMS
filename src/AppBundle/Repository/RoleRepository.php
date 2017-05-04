<?php

namespace AppBundle\Repository;

class RoleRepository extends \Doctrine\ORM\EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
