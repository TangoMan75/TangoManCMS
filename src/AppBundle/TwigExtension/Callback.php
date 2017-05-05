<?php

namespace AppBundle\TwigExtension;

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
            foreach ($query as $key => $item) {
                if ($key == 'callback') {
                    $callbackQuery = $item;
                } else {
                    $arNewQuery[$key] = $item;
                    $strNewQuery = http_build_query($arNewQuery);
                }
            }
        }

        $url = $result['scheme'].'://'.
            (isset($result['user']) ? $result['user'] : '').
            (isset($result['pass']) ? ':'.$result['pass'].'@' : '').
            $result['host'].
            (isset($result['port']) ? ':'.$result['port'] : '').
            (isset($result['path']) ? $result['path'] : '').
            (isset($strNewQuery) ? '?'.$strNewQuery : '').
            (isset($result['fragment']) ? '#'.$result['fragment'] : '');

        return $url;
    }
}
