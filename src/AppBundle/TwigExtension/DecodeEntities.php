<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\TwigExtension;

use Symfony\Component\Debug\Exception\UndefinedFunctionException;

/**
 * Class DecodeEntities
 * Remove specified tags
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
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