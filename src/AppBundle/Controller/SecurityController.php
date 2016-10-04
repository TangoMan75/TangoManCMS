<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class SecurityController
 * @package AppBundle\Controller
 * @Route("/security")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('default/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error'         => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/check", name="app_check")
     */
    public function checkAction()
    {
    }

    /**
     * @Route("/reset", name="app_reset")
     */
    public function resetAction()
    {
    }

    /**
     * @Route("/password/{token}", name="app_password")
     */
    public function passwordAction(Request $request, $token)
    {
        $user = $this->em()->getRepository('AppBundle:User')->findOneBy(['token'=>$token]);

        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            // Deletes token
            $user->setToken(null);

            $em = $this->em();
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Un nouveau mot de passe à bien été créé pour le compte {$user->getUsername()}.");

            // Starts user session
            $sessionToken = new UsernamePasswordToken($user, null, 'database', $user->getRoles());
            $this->get('security.token_storage')->setToken($sessionToken);
            $this->get('session')->set('_security_main',serialize($sessionToken));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('user/password.html.twig', [
            'form_password' => $form->createView()
        ]);
    }

}
