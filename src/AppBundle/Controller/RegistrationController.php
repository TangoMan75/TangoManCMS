<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="app_register")
     */
    public function registerAction()
    {
        $user = new User();

        return $this->render('AppBundle:Registration:register.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/edit", name="app_edit")
     */
    public function editAction()
    {
        return $this->render('AppBundle:Registration:edit.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/confirm", name="app_confirm")
     */
    public function confirmAction()
    {
        return $this->render('AppBundle:Registration:confirm.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/show", name="app_show")
     */
    public function showAction()
    {
        return $this->render('AppBundle:Registration:show.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/delete", name="app_delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Registration:delete.html.twig', array(
            // ...
        ));
    }

}
