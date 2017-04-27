<?php

namespace AppBundle\TwigExtension;

/**
 * Class RemoveUrlQuery
 * Removes query string from url.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\TwigExtension
 */
class RemoveQuery extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'remove_query';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('remove_query', [$this, 'removeQuery']),
        ];
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function removeQuery($url)
    {
        $result = parse_url($url);

        $url = $result['scheme'].'://'.$result['host'].
            (isset($result['path']) ? $result['path'] : '').
            (isset($result['fragment']) ? '#'.$result['fragment'] : '');

        return $url;
    }

}
