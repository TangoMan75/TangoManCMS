<?php

namespace TangoMan\MenuBundle\Twig\Extension;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $template;

    /**
     * MenuExtension constructor.
     *
     * @param \Twig_Environment $template
     */
    public function __construct(\Twig_Environment $template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tangoman_menu';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'menu', [$this, 'menuFunction'], ['is_safe' => ['html'],]
            ),
        ];
    }

    /**
     * @param string    $template
     *
     * @return string
     */
    public function menuFunction($menu, $template = 'default')
    {
        $templates = [
            'default',
            'sidebar',
            'tabs',
        ];

        if (in_array($template, $templates)) {
            $template = '@TangoManMenu/'.$template.'.html.twig';
        }

        return $this->template->render(
            $template,
            [
                'menu' => $menu,
            ]
        );
    }
}
