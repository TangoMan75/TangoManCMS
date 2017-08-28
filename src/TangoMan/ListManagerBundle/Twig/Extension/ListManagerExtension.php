<?php

namespace TangoMan\ListManagerBundle\Twig\Extension;

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
     * @return string
     */
    public function listManagerFunction()
    {
        return $this->template->render(
            '@TangoManListManager/index.html.twig', []
        );
    }
}
