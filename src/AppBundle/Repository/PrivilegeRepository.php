<?php

namespace AppBundle\Repository;

class PrivilegeRepository extends \Doctrine\ORM\EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\Name;
}
