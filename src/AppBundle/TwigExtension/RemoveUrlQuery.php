<?php

namespace AppBundle\TwigExtension;

/**
 * Class RemoveUrlQueryExtension.
 * Removes query string from url.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\TwigExtension
 */
class RemoveUrlQuery extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'remove_url_query';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('remove_url_query', [$this, 'removeUrlQuery']),
        ];
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function removeUrlQuery($url)
    {
        return explode('?', $url)[0];
    }

}
