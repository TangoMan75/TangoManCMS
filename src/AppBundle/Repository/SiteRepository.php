<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class SiteRepository
 *
 * @package AppBundle\Repository
 */
class SiteRepository extends EntityRepository
{

    use RepositoryHelper;
}
