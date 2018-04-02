<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Service;

/**core/pdo.php
 */
class HostHelperService
{

    /**
     * @return string
     */
    public function getDomainLabel()
    {
        return explode('.', $_SERVER["SERVER_NAME"])[0];
    }

    /**
     * @return bool
     */
    public function isApi()
    {
        if ($this->getDomainLabel() == 'api') {
            return true;
        }

        return false;
    }
}
