<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SecurityController
 * @package AppBundle\Controller
 * @Route("/access")
 */
class AccessController extends Controller
{
    /**
     * Build login form.
     *
     * @Route("/login", name="app_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');
        $error = $helper->getLastAuthenticationError();

        if ($error) {
            $this->get('session')->getFlashBag()->add('error', $error);
            $this->get('session')->getFlashBag()->add('translate', 'true');
        }

        return $this->render('default/login.html.twig', [
            'last_username' => $helper->getLastUsername()
        ]);
    }

    /**
     * Abstract method required by symfony core.
     *
     * @Route("/logout", name="app_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * Abstract method required by symfony core.
     *
     * @Route("/check", name="app_check")
     */
    public function checkAction()
    {
    }
}
