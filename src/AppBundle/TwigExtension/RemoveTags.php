<?php

namespace AppBundle\TwigExtension;

use Symfony\Component\Debug\Exception\UndefinedFunctionException;

/**
 * Class RemoveTags
 * Remove specified tags
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package AppBundle\TwigExtension
 */
class RemoveTags extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'remove_tags';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('remove_tags', [$this, 'removeTags']),
        ];
    }

    /**
     * @param        $string
     * @param string $tags
     * @param bool   $invert
     *
     * @return mixed|string
     */
    function removeTags($string, $tags = '', $invert = false)
    {
        $string = tidy_repair_string(
            str_replace(["\r", "\n"], "", html_entity_decode($string)),
            [
                'output-xml'      => true,
                'input-xml'       => true,
                'input-encoding'  => 'ISO8859-1',
                'output-encoding' => 'UTF8',
            ]
        );

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) AND count($tags) > 0) {
            if ($invert == false) {
                $test = '<(?!(?:'.implode('|', $tags).')\b)(\w+)\b.*?';

                return preg_replace('@'.$test.'>.*?</\1>|'.$test.'/>@si', '', $string);
            } else {
                $test = '<('.implode('|', $tags).')\b.*?';

                return preg_replace('@'.$test.'>.*?</\1>|'.$test.'/>@si', '', $string);
            }
        } elseif ($invert == false) {
            $test = '<(\w+)\b.*?';

            return preg_replace('@'.$test.'>.*?</\1>|'.$test.'/>@si', '', $string);
        }

        return $string;
    }

}
