<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class PrivilegeController
 * @Route("/admin/privileges")
 *
 * @package AppBundle\Controller
 */
class PrivilegeController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {
        return $this->render(
            'admin/privilege/index.html.twig',
            [
                // ...
            ]
        );
    }

    /**
     * @Route("/show")
     */
    public function showAction()
    {
        return $this->render(
            'admin/privilege/show.html.twig',
            [
                // ...
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction()
    {
        return $this->render(
            'admin/privilege/new.html.twig',
            [
                // ...
            ]
        );
    }

    /**
     * @Route("/edit")
     */
    public function editAction()
    {
        return $this->render(
            'admin/privilege/edit.html.twig',
            [
                // ...
            ]
        );
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction()
    {
        return $this->render(
            'admin/privilege/delete.html.twig',
            [
                // ...
            ]
        );
    }

}
