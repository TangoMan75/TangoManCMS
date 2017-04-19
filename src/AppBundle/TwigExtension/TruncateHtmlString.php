<?php

namespace AppBundle\TwigExtension;

/**
 * Class TruncateHtmlString
 *
 * @package AppBundle\TwigExtension
 */
class TruncateHtmlString
{
    /**
     * @var \DOMDocument
     */
    private $newDiv;

    /**
     * TruncateHtmlString constructor.
     *
     * @param $string
     * @param $limit
     */
    function __construct($string, $limit)
    {
        // create dom element using the html string
        $this->tempDiv = new \DomDocument();
        $string = tidy_repair_string(
            str_replace(["\r", "\n"], "", html_entity_decode($string)),
            [
                'output-xml'      => true,
                'input-xml'       => true,
                'input-encoding'  => 'ISO8859-1',
                'output-encoding' => 'UTF8',
            ]
        );
        $this->tempDiv->loadXML('<div>'.$string.'</div>');
        // keep the characters count till now
        $this->charCount = 0;
        $this->encoding = 'UTF-8';
        // character limit need to check
        $this->limit = $limit;
    }

    /**
     * @param $endChar
     *
     * @return string
     */
    function cut($endChar)
    {
        // create empty document to store new html
        $this->newDiv = new \DomDocument();
        // cut the string by parsing through each element
        $this->searchEnd($this->tempDiv->documentElement, $this->newDiv, $endChar);
        $newhtml = $this->newDiv->saveHTML();

        return $newhtml;
    }

    /**
     * @param \DOMNode $node
     */
    function deleteChildren(\DOMNode $node)
    {
        while (isset($node->firstChild)) {
            $this->deleteChildren($node->firstChild);
            $node->removeChild($node->firstChild);
        }
    }

    /**
     * @param $parseDiv
     * @param $newParent
     * @param $endChar
     *
     * @return bool
     */
    function searchEnd($parseDiv, $newParent, $endChar)
    {
        foreach ($parseDiv->childNodes as $ele) {
            // not text node
            if ($ele->nodeType != 3) {
                $newEle = $this->newDiv->importNode($ele, true);
                if (count($ele->childNodes) === 0) {
                    $newParent->appendChild($newEle);
                    continue;
                }
                $this->deleteChildren($newEle);
                $newParent->appendChild($newEle);
                $res = $this->searchEnd($ele, $newEle, $endChar);
                if ($res) {
                    return $res;
                } else {
                    continue;
                }
            }

            // the limit of the char count reached
            if (mb_strlen($ele->nodeValue, $this->encoding) + $this->charCount >= $this->limit) {
                $newEle = $this->newDiv->importNode($ele);
                $newEle->nodeValue = substr($newEle->nodeValue, 0, $this->limit - $this->charCount).$endChar;
                $newParent->appendChild($newEle);

                return true;
            }
            $newEle = $this->newDiv->importNode($ele);
            $newParent->appendChild($newEle);
            $this->charCount += mb_strlen($newEle->nodeValue, $this->encoding);
        }

        return false;
    }
}