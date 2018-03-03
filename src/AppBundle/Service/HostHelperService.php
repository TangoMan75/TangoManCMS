<?php

namespace AppBundle\Service;

/**
 * @package AppBundle\Services
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
