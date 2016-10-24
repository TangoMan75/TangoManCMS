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

    /**
     * Send email containing password reset security token.
     *
     * @return mixed
     * @Route("/token", name="app_token")
     */
    public function tokenRequestAction(Request $request)
    {
        $form = $this->createForm(\AppBundle\Form\EmailType::class);
        $form->handleRequest($request);

        // When form is submitted
        if ( $form->isSubmitted() && $form->isValid() ) {
            $email = $form->getData()['email'];
            $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['email' => $email]);

            // Send error message when user not found
            if ( !$user ) {
                $this->get('session')->getFlashBag()->add('error', "Cet utilisateur n'exite pas.");
                return $this->redirectToRoute('app_token');
            }

            // Generate password reset token
            $jwt = new JWT();
            $jwt->set('email', $email);
            $jwt->set('action', 'reset');
            $jwt->setPeriod(new \DateTime(), new \DateTime('+1 days'));
            $token = $this->get('jwt')->encode($jwt);

            // Sends validation email to user
            $message = \Swift_Message::newInstance()
                ->setSubject("Livre D'Or | Réinitialisation de mot de passe.")
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo($email)
                ->setBody(
                    $this->renderView('email/reset.html.twig', [
                        'user' => $user,
                        'token' => $token
                    ]),
                    'text/html'
                )
            ;

            $this->get('mailer')->send($message);
            $this->get('session')->getFlashBag()->add('success', "Votre demande de renouvellement de mot de passe a ".
                "bien été prise en compte.<br />Un lien de comfirmation vous à été envoyé à <strong>$email</strong>. ".
                "<br /> Vérifiez votre boîte email.");
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
        // JSON Web Token validation
        $jwt = $this->get('jwt')->decode($token);

        $username = $jwt->get('username');
        $email    = $jwt->get('email');
        $action   = $jwt->get('action');

        // Display error message when token is invalid
        if ( !$jwt->isValid() || $action != "create" && $action != "reset" ) {
            $this->get('session')->getFlashBag()->add('error', "Désolé <strong>$username</strong><br />".
                "Votre lien de sécurité n'est pas valide ou à expiré.<br />".
                "Vous devez recommencer le procéssus d'inscription."
            );
            return $this->redirectToRoute('app_homepage');
        }

        if ($action = "create") {
            // Create new user
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
        }

        if ($action = "reset") {
            // Find user
            $user = $this->get('em')->repository('AppBundle:User')->findOneByUsername($username);
        }

        // Generate form
        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        // Check form validation
        if ( $form->isValid() ) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            // Persist password
            $this->get('em')->save($user);

            $this->get('session')->getFlashBag()->add('success',
                "Un nouveau mot de passe à bien été créé pour le compte <strong>{$user->getUsername()}</strong>."
            );

            // Start user session
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
//    public function resendToken(Request $request, User $user)
//    {
//        // Only admins are allowed to perform this action
//        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
//            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à faire cette action.");
//            return $this->redirectToRoute('app_homepage');
//        }
//
//        $username = $user->getUsername();
//        $email    = $user->getEmail();
//
//        // Generate JSON Web Token
//        $jwt = new JWT();
//        $jwt->set('email', $email);
//        $jwt->set('action', 'password');
//        $jwt->setPeriod(new \DateTime(), new \DateTime('+1 days'));
//        $token = $this->get('jwt')->encode($jwt);
//
//        $message = \Swift_Message::newInstance()
//            ->setSubject("Livre D'Or | Confirmation d'inscription.")
//            ->setFrom($this->getParameter('mailer_from'))
//            ->setTo($user->getEmail())
//            ->setBody(
//                $this->renderView('email/validation.html.twig', [
//                    'user' => $user,
//                    'token' => $token
//                ]),
//                'text/html'
//            )
//        ;
//        $this->get('mailer')->send($message);
//        $this->get('session')->getFlashBag()->add('success', "Un nouveau mail de confirmation à été envoyé à ".
//            "<strong>$username</strong>.");
//
//        return $this->redirectToRoute('user_index');
//    }

    /**
     * Force validate user.
     *
     * @Route("/validate/{id}", requirements={"id": "\d+"}, name="app_validate")
     */
//    public function validateUser(Request $request, User $user)
//    {
//        // Only admins are allowed to perform this action
//        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
//            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
//            return $this->redirectToRoute('app_homepage');
//        }
//
//        // Removes token
//        $user->setToken(null);
//        $this->get('em')->save($user);
//        $this->get('session')->getFlashBag()->add('success', "L'utilisateur <strong>{$user->getUsername()}</strong> ".
//            "à été validé.");
//
//        return $this->redirectToRoute('user_index');
//    }

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
     * @Route("/encode")
     */
    public function setTokenAction()
    {
        $jwtService = $this->get('jwt');

        $jwt = new JWT();
        $jwt->set('email', 'admin@example.com');
        $jwt->set('username', 'Admin');
        $jwt->setPeriod(new \DateTime(), new \DateTime('+3 days'));
        $token = $jwtService->encode($jwt);

        $jwt2 = new JWT();
        $jwt2->set('email', 'admin@example.com');
        $jwt2->set('username', 'Admin');
        $jwt2->setPeriod(new \DateTime('+1 day'), new \DateTime('+3 days'));
        $token2 = $jwtService->encode($jwt2);

        $jwt3 = new JWT();
        $jwt3->set('email', 'admin@example.com');
        $jwt3->set('username', 'Admin');
        $jwt3->setPeriod(new \DateTime('-3 days'), new \DateTime('-1 days'));
        $token3 = $jwtService->encode($jwt3);

        dump($jwt);
        dump($token);
        dump($jwt2);
        dump($token2);
        dump($jwt3);
        dump($token3);
        die();

        return new JsonResponse( array($token, $token2, $token3) );
    }

    /**
     * getTokenAction
     *
     * @Route("/decode/{token}")
     */
    public function getTokenAction(Request $request, $token)
    {
        $jwt = $this->get('jwt')->decode($token);

//        dump($jwt);
//        dump($token);
//        die();

        if (!$jwt->isSignatureValid()){
            throw $this->createNotFoundException("La signature du token n'est pas valide.");
        }

        if ($jwt->isTooSoon()){
            throw $this->createNotFoundException("Le token n'est pas encore valide.");
        }

        if ($jwt->isTooLate()){
            throw $this->createNotFoundException("Le token est expiré.");
        }

        return new JsonResponse( array('success' => "Ce token est valide.") );
    }
}
