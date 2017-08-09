<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class SectionRepository
 *
 * @package AppBundle\Repository
 */
class SectionRepository extends EntityRepository
{
    use RepositoryHelper;
}
