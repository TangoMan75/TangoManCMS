<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class PostRepository
 *
 * @package AppBundle\Repository
 */
class PostRepository extends EntityRepository
{
    use RepositoryHelper;
}
