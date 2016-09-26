<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegistrationController extends Controller
{
    /**
     * @Route("/register")
     */
    public function registerAction()
    {
        return $this->render('AppBundle:Registration:register.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/edit")
     */
    public function editAction()
    {
        return $this->render('AppBundle:Registration:edit.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/confirm")
     */
    public function confirmAction()
    {
        return $this->render('AppBundle:Registration:confirm.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/show")
     */
    public function showAction()
    {
        return $this->render('AppBundle:Registration:show.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Registration:delete.html.twig', array(
            // ...
        ));
    }

}
