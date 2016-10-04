<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 * @package AppBundle\Controller
 * @Route("/security")
 */
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
     * @Route("/password/{$token}", name="app_password")
     */
    public function setPasswordAction(Request $request, $token)
    {
        $user = new User;

        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {

//            $encoder = $this->get('security.password_encoder');
//            $encoded = $encoder->encodePassword($user, $user->getPassword());
//            $user->setPassword($encoded);

            $username = $user->getUsername();
            $email    = $user->getEmail();

            // Generates token from username and unix time
            $user->setToken(md5(time().$username));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', "Merci $username, votre demande d'inscription a bien été prise en compte.<br />Un lien de comfirmation vous à été envoyé à $email. <br /> Vérifiez votre boîte email.");

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('user/register.html.twig', [
            'form_register' => $form->createView(),
        ]);
    }

}
