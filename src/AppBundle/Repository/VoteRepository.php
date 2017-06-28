<?php

namespace AppBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Class LikeRepository
 *
 * @package AppBundle\Repository
 */
class VoteRepository extends EntityRepository
{
    use Traits\RepositoryHelper;
}
