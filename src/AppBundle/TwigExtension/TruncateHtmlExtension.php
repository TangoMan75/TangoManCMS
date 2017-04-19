<?php

namespace AppBundle\TwigExtension;

use Symfony\Component\Debug\Exception\UndefinedFunctionException;

/**
 * Class TruncateHtmlExtension
 * Truncates html keeping tags
 *
 * @package AppBundle\TwigExtension
 */
class TruncateHtmlExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'truncatehtml';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('truncatehtml', [$this, 'truncatehtml'], ['needs_environment' => true])];
    }

    /**
     * @param \Twig_Environment $environment
     * @param string            $html
     * @param int               $limit
     * @param bool              $endchar
     *
     * @return string
     */
    public function truncatehtml(\Twig_Environment $environment, $html, $limit, $endchar = false)
    {
        if ($endchar === false) {
            $endchar = html_entity_decode("&hellip;");
        }
        try {
            if (!is_callable('tidy_repair_string')) {
                throw new UndefinedFunctionException(
                    'Missing tidy_repair_string function from tidy library.',
                    new \ErrorException()
                );
            }
            $output = new TruncateHtmlString($html, $limit);

            return $output->cut($endchar);
        } catch (\Exception $e) {
            return nl2br(twig_truncate_filter($environment, strip_tags($html), $limit, false, $endchar));
        }
    }
}