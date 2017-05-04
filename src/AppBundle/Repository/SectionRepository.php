<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class SectionRepository extends \Doctrine\ORM\EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;
}
