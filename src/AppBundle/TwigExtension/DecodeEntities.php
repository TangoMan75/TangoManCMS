<?php

namespace AppBundle\TwigExtension;

use Symfony\Component\Debug\Exception\UndefinedFunctionException;

/**
 * Class DecodeEntities
 * Remove specified tags
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package AppBundle\TwigExtension
 */
class DecodeEntities extends \Twig_Extension
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'decode_entities';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'decode_entities', [$this, 'decodeEntities']
            ),
        ];
    }

    /**
     * @param $string
     *
     * @return string
     */
    function decodeEntities($string)
    {

        return html_entity_decode($string);
    }
}