<?php

namespace AppBundle\TwigExtension;


/**
 * Class RemoveUrlQueryExtension.
 *
 * Removes query string from url.
 * @package AppBundle\TwigExtension
 */
class RemoveUrlQuery extends \Twig_Extension
{

    public function getName()
    {
        return 'remove_url_query';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('remove_url_query', [$this, 'removeUrlQuery'])
        ];
    }

    /**
     * @param $url
     * @return string
     */
    public function removeUrlQuery($url)
    {
        return explode('?', $url)[0];
    }

}
