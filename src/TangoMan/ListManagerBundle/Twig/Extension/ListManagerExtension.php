<?php

namespace TangoMan\ListManagerBundle\Twig\Extension;

use TangoMan\ListManagerBundle\Model\SearchForm;
use TangoMan\ListManagerBundle\Model\SearchInput;
use TangoMan\ListManagerBundle\Model\SearchOption;

class ListManagerExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $template;

    /**
     * @return string
     */
    public function getName()
    {
        return 'tangoman_listmanager';
    }

    /**
     * ListManagerExtension constructor.
     *
     * @param \Twig_Environment $template
     */
    public function __construct(\Twig_Environment $template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'listManager', [$this, 'listManagerFunction'], ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param        $form
     * @param string $template
     *
     * @return string
     */
    public function listManagerFunction($form, $template = 'search')
    {
        if ($template == 'search' || $template == 'order') {
            $template = '@TangoManListManager/'.$template.'.html.twig';
        }

        return $this->template->render(
            $template, [
                         'form' => $form,
                     ]
        );
    }
}
