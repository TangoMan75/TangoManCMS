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
        // action_entity_property
        $result = [
            'action'   => null,
            'entity'   => null,
            'property' => null,
        ];

        // a  : andWhere
        // b  : orderBy
        // c  : count
        // e  : exact match
        // j  : join
        // ja : join + andWhere
        // jb : join + orderBy
        // jo : join + orWhere
        // n  : not null
        // o  : orWhere
        // s  : simple array
        $validActions = ['a', 'b', 'c', 'e', 'j', 'ja', 'jb', 'jo', 'n', 'o', 's'];

        $temp = explode('-', $string);

        switch (count($temp)) {
            case 1:
                $result['property'] = $temp[0];
                break;

            case 2:
                if (in_array($temp[0], $validActions)) {
                    $result['action'] = $temp[0];
                    $result['property'] = $temp[1];
                } else {
                    $result['entity'] = $temp[0];
                    $result['property'] = $temp[1];
                }
                break;

            case 3:
                $result['action'] = $temp[0];
                $result['entity'] = $temp[1];
                $result['property'] = $temp[2];
                break;
        }

        return $result;
    }
}
