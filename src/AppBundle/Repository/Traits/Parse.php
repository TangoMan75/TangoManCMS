<?php

namespace AppBundle\Repository\Traits;

/**
 * Trait Parse
 * Parses string like "j_"
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository\Traits
 */
Trait Parse
{
    /**
     * @return array
     */
    public function parse($string)
    {
        // action_entity.property
        $result = [
            'action'   => null,
            'entity'   => null,
            'property' => null,
        ];

        if (stripos($string, '_') > 0) {
            $result['action'] = strstr($string, '_', true);
            $string = ltrim(strstr($string, '_'), '_');
        }

        if (stripos($string, ':') > 0) {
            $result['entity'] = strstr($string, ':', true);
            $result['property'] = ltrim(strstr($string, ':'), ':');
        } else {
            $result['property'] = $string;
        }

        return $result;
    }
}
