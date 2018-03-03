<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class TagRepository
 *
 * @package AppBundle\Repository
 */
class TagRepository extends EntityRepository
{

    use RepositoryHelper;
}
