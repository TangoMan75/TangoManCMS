<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class LikeRepository
 *
 * @package AppBundle\Repository
 */
class VoteRepository extends EntityRepository
{
    use RepositoryHelper;
}
