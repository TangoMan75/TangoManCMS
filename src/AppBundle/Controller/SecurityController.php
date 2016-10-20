<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use AppBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Builds login form.
     *
     * @Route("/login", name="app_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');
        $error = $helper->getLastAuthenticationError();

        if ($error) {

            $this->get('session')->getFlashBag()->add('error',$error);
            $this->get('session')->getFlashBag()->add('translate','true');

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

    /**
     * Sends email containing password reset security token.
     *
     * @return mixed
     * @Route("/token", name="app_token")
     */
    public function tokenRequestAction(Request $request)
    {
        $form = $this->createForm(\AppBundle\Form\EmailType::class);
        $form->handleRequest($request);

        // When form is submitted
        if ( $form->isSubmitted() ) {

            $email = $form->getData()['email'];
            $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['email' => $email]);

            // Sends error message when user not found
            if ( !$user ) {

                $this->get('session')->getFlashBag()->add('error', "Cet utilisateur n'exite pas.");
                return $this->redirectToRoute('app_token');
            }

            $user->setToken(md5(uniqid()));

            // Saves token in base
            $this->get('em')->save($user);

            // Sends validation email to user
            $message = \Swift_Message::newInstance()
                ->setSubject("Livre D'Or | Réinitialisation de mot de passe.")
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo($email)
                ->setBody(
                    $this->renderView('email/reset.html.twig', [
                        'user' => $user
                    ]),
                    'text/html'
                )
            ;

            $this->get('mailer')->send($message);
            $this->get('session')->getFlashBag()->add('success', "Votre demande de renouvellement de mot de passe a ".
                "bien été prise en compte.<br />Un lien de ".
                "comfirmation vous à été envoyé à ".
                "<strong>$email</strong>. <br /> Vérifiez votre ".
                "boîte email.");
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('user/reset.html.twig', [
            'form_reset' => $form->createView()
        ]);
    }

    /**
     * Checks security token and allows password change.
     *
     * @Route("/password/{token}", name="app_password")
     */
    public function passwordAction(Request $request, $token)
    {
        $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['token'=>$token]);

        // Displays error message when token is invalid
        if ( !$user ) {

            $this->get('session')->getFlashBag()->add('error', "Votre lien de sécurité n'est pas valide ou à expiré.");
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        if ( $form->isValid() ) {

            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            // Deletes token
            $user->setToken(null);
            $this->get('em')->save($user);
            $this->get('session')->getFlashBag()->add('success', "Un nouveau mot de passe à bien été créé pour le ".
                "compte <strong>{$user->getUsername()}</strong>.");
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

    /**
     * Re-sends security token to user.
     *
     * @Route("/resend/{id}", requirements={"id": "\d+"}, name="app_resend_token")
     */
    public function resendToken(Request $request, User $user)
    {
        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à faire cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        $username = $user->getUsername();
        $email    = $user->getEmail();
        // Generates token from username and unix time
        $user->setToken(md5(time().$username));
        $this->get('em')->save($user);
        $message = \Swift_Message::newInstance()
            ->setSubject("Livre D'Or | Confirmation d'inscription.")
            ->setFrom($this->getParameter('mailer_from'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('email/validation.html.twig', [
                    'user' => $user
                ]),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
        $this->get('session')->getFlashBag()->add('success', "Un nouveau mail de confirmation à été envoyé à ".
            "<strong>$username</strong>.");

        return $this->redirectToRoute('user_index');
    }

    /**
     * Force validate user.
     *
     * @Route("/validate/{id}", requirements={"id": "\d+"}, name="app_validate")
     */
    public function validateUser(Request $request, User $user)
    {
        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à faire cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        // Removes token
        $user->setToken(null);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "L'utilisateur <strong>{$user->getUsername()}</strong> ".
            "à été validé.");

        return $this->redirectToRoute('user_index');
    }

    /**
     * Finds and deletes user.
     *
     * @Route("/delete/{token}", name="app_delete")
     */
    public function deleteAction(Request $request, $token)
    {
        // Gets user entity by token
        $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['token'=>$token]);

        if ( !$user ) {

            $this->get('session')->getFlashBag()->add('error', "Votre lien de sécurité n'est pas valide ou à expiré.");
            return $this->redirectToRoute('app_homepage');

        } else {
            // Removes user
            $this->get('em')->remove($user);
            $this->get('em')->flush();

            // Sends success message
            $this->get('session')->getFlashBag()->add('success', "L'utilisateur <strong>{$user->getUsername()}".
                "</strong> à bien été supprimé.");

            // Disconnects user who deletes his own account
            if ( $user == $this->getUser() ) {

                $this->get('security.token_storage')->setToken(null);
                $request->getSession()->invalidate();
                return $this->redirectToRoute('app_homepage');
            }

            return $this->redirectToRoute('app_homepage');
        }
    }

    /**
     * setTokenAction
     *
     * @param string $token
     * @Route("/enc-token")
     */
    public function setTokenAction()
    {
        $jwt = new JWT();

        $jwt->setData('email', 'admin@example.com');
        $jwt->setData('name', 'Admin');
        $jwt->setPeriod(new \DateTime('+1 second'), new \DateTime('+3 days'));

        $token = $this->get('jwt')->encode($jwt);
        dump($jwt);

        return new JsonResponse( array($token) );
    }

    /**
     * getTokenAction
     *
     * @Route("/dec-token/{token}")
     */
    public function getTokenAction(Request $request, $token)
    {
        $jwtService = $this->get('jwt');
        $jwt = $jwtService->decode($token);

        dump($jwt);
        dump($token);

        if ( !$jwtService->validate($jwt) ) {
            return new JsonResponse( array( $jwtService->getErrors() ));
        }

        return new JsonResponse( array('success' => "Ce token est valide.") );
    }
}
