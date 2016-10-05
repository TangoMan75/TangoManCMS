<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
     * @return mixed
     * @Route("/token", name="app_token")
     */
    public function tokenRequestAction(Request $request)
    {
        $form = $this->createForm(\AppBundle\Form\EmailType::class);
        $form->handleRequest($request);

        // When form is submitted
        if ($form->isSubmitted()) {

            $email = $form->getData()['email'];
            // ou
            // $email = $request->get('email');

            $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['email' => $email]);

            // Sends error message when user not found
            if (!$user) {

                $this->get('session')->getFlashBag()->add('error', "Cet utilisateur n'exite pas.");

                return $this->redirectToRoute('app_token');

            } else {

                // Generates token from username and unix time
                $user->setToken(md5(time().$user->getUsername()));

                $this->get('em')->save($user);

                $message = \Swift_Message::newInstance()
                    ->setSubject("Livre D'Or | Réinitialisation de mot de passe.")
                    ->setFrom($this->getParameter('mailer_from'))
                    ->setTo($email)
                    ->setBody(
                        $this->renderView('email/reset.html.twig', [
                            'user' => $user
                        ]),
                        'text/html'
                    );

                $this->get('mailer')->send($message);
                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('user/reset.html.twig', [
            'form_reset' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/{token}", name="app_password")
     */
    public function passwordAction(Request $request, $token)
    {
        $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['token'=>$token]);

        // Displays error message when token is invalid
        if (!$user) {

            $this->get('session')->getFlashBag()->add('error', "Votre lien de sécurité n'est pas valide ou à expiré.");
            return $this->redirectToRoute('app_homepage');

        } else {

            $form = $this->createForm(PwdType::class, $user);
            $form->handleRequest($request);

            if ($form->isValid()) {

                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);

                // Deletes token
                $user->setToken(null);

                $this->get('em')->save($user);

                $this->get('session')->getFlashBag()->add('success', "Un nouveau mot de passe à bien été créé pour le compte <strong>{$user->getUsername()}</strong>.");

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

}
