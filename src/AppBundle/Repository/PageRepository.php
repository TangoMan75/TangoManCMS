<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class PageRepository
 *
 * @package AppBundle\Repository
 */
class PageRepository extends EntityRepository
{

    use RepositoryHelper;
}
