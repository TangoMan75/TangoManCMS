<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="app_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('default/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logoutAction()
    {
        return $this->render('AppBundle:security:logout.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/check", name="app_check")
     */
    public function checkAction()
    {
        return $this->render('AppBundle:security:check.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/reset", name="app_reset")
     */
    public function resetAction()
    {
        return $this->render('AppBundle:security:reset.html.twig', array(
            // ...
        ));
    }

}
