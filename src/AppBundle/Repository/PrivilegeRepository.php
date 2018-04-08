<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TangoMan\RepositoryHelper\RepositoryHelper;

/**
 * Class PrivilegeRepository
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class PrivilegeRepository extends EntityRepository
{

    use RepositoryHelper;
}
