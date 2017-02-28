<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class PageController
 * @Route("/admin/pages")
 *
 * @package AppBundle\Controller
 */
class PageController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {
        return $this->render(
            'admin/page/index.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction()
    {
        return $this->render(
            'admin/page/new.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/edit")
     */
    public function editAction()
    {
        return $this->render(
            'admin/page/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction()
    {
        return $this->render(
            'admin/page/delete.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

}
