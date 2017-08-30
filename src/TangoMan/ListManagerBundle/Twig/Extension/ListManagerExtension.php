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
                'searchForm', [$this, 'searchFormFunction'], ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'orderFields', [$this, 'orderFieldsFunction'], ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param        $form
     * @param string $template
     *
     * @return string
     */
    public function searchFormFunction($form, $template = 'search')
    {
        if ($template == 'search') {
            $template = '@TangoManListManager/'.$template.'.html.twig';
        }

        if (is_string($form)) {
            $form = json_decode($form);
        }

        return $this->template->render(
            $template, [
                         'form' => $form,
                     ]
        );
    }

    /**
     * @param        $order
     * @param string $template
     *
     * @return string
     */
    public function orderFieldsFunction($order, $template = 'order')
    {
        if ($template == 'order') {
            $template = '@TangoManListManager/'.$template.'.html.twig';
        }

        if (is_string($order)) {
            $order = json_decode($order);
        }

        return $this->template->render(
            $template, [
                         'order' => $order,
                     ]
        );
    }
}
