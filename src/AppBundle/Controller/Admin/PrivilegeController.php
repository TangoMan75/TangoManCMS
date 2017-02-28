<?php

namespace AppBundle\Controller\Admin;

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
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render(
            'admin/privilege/index.html.twig',
            [
                'currentUser' => $this->getUser(),
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
            'admin/privilege/new.html.twig',
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
            'admin/privilege/edit.html.twig',
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
            'admin/privilege/delete.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

}
