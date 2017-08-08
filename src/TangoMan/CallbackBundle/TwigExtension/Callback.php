<?php

namespace TangoMan\CallbackBundle\TwigExtension;

/**
 * Class Callback
 * Avoids multiple callbacks appending indefinitely.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\TwigExtension
 */
class Callback extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'callback';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('callback', [$this, 'callback']),
        ];
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function callback($url)
    {
        $result = parse_url($url);

        // When url contains query string
        if (isset($result['query'])) {

            parse_str($result['query'], $query);

            // Remove callback from query
            $query = array_diff_key($query, ['callback' => null]);

        } else {
            // Return unchanged url
            return $url;
        }

        return $result['scheme'].'://'.
            (isset($result['user']) ? $result['user'] : '').
            (isset($result['pass']) ? ':'.$result['pass'].'@' : '').
            $result['host'].
            (isset($result['port']) ? ':'.$result['port'] : '').
            (isset($result['path']) ? $result['path'] : '').
            ($query != [] ? '?'.http_build_query($query) : '').
            (isset($result['fragment']) ? '#'.$result['fragment'] : '');
    }
}
