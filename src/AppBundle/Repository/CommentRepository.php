<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class CommentRepository
 *
 * @package AppBundle\Repository
 */
class CommentRepository extends EntityRepository
{
    use RepositoryHelper;
}
