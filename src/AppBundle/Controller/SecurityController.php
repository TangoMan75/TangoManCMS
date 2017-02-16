<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use TangoMan\JWTBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @Route("/security")
 */
class SecurityController extends Controller
{
    /**
     * Send email containing password reset security token.
     *
     * @return mixed
     * @Route("/token")
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
                return $this->redirectToRoute('app_security_tokenrequest');
            }

            // Generate password reset token
            $jwt = new JWT();
            $jwt->set('id', $user->getId());
            $jwt->set('username', $user->getUsername());
            $jwt->set('email', $email);
            $jwt->set('action', 'reset');
            $jwt->setPeriod(new \DateTime(), new \DateTime('+1 day'));
            $token = $this->get('tangoman_jwt')->encode($jwt);

            // Sends validation email to user
            $message = \Swift_Message::newInstance()
                ->setSubject($this->getParameter('site_name') . " | Réinitialisation de mot de passe.")
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
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/reset.html.twig', [
            'form_reset' => $form->createView()
        ]);
    }

    /**
     * Send email containing password reset security token.
     *
     * @return mixed
     * @Route("/password-change/{id}", requirements={"id": "\d+"})
     */
    public function passwordChangeAction(Request $request, User $user)
    {
        // Send error message when user not found
        if ( !$user ) {
            $this->get('session')->getFlashBag()->add('error', "Cet utilisateur n'exite pas.");
            return $this->redirectToRoute('app_security_tokenrequest');
        }

        // Only user is allowed to change his password
        if ( $this->getUser() == $user ) {
            $email = $user->getEmail();

            // Generate password reset token
            $jwt = new JWT();
            $jwt->set('id', $user->getId());
            $jwt->set('username', $user->getUsername());
            $jwt->set('email', $email);
            $jwt->set('action', 'reset');
            $jwt->setPeriod(new \DateTime(), new \DateTime('+1 day'));
            $token = $this->get('tangoman_jwt')->encode($jwt);

            // Sends validation email to user
            $message = \Swift_Message::newInstance()
                ->setSubject($this->getParameter('site_name') . " | Réinitialisation de mot de passe.")
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
        }

        // User is redirected to referrer page
        return $this->redirect( $request->get('callback') );
    }

    /**
     * Checks security token and allows password change.
     *
     * @Route("/password/{token}")
     */
    public function passwordAction(Request $request, $token)
    {
        // JSON Web Token validation
        $jwt = $this->get('tangoman_jwt')->decode($token);

        $username = $jwt->get('username');
        $email    = $jwt->get('email');
        $action   = $jwt->get('action');

        // Display error message when token is invalid
        if ( !$jwt->isValid() || $action != "create" && $action != "reset" ) {
            $this->get('session')->getFlashBag()->add('error', "Désolé <strong>$username</strong><br />".
                "Votre lien de sécurité n'est pas valide ou à expiré.<br />".
                "Vous devez recommencer le procéssus d'inscription."
            );
            return $this->redirectToRoute('homepage');
        }

        if ($action == "create") {
            // Create new user
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
        }

        if ($action == "reset") {
            // Find user
            $user = $this->get('em')->repository('AppBundle:User')->find($jwt->get('id'));
            // When user doesn't exist
            if (!$user) {
                $this->get('session')->getFlashBag()->add('error',
                    "Désolé <strong>$username</strong> ce compte a été supprimé."
                );
                return $this->redirectToRoute('homepage');
            }
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

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/password.html.twig', [
            'form_password' => $form->createView()
        ]);

    }

    /**
     * Finds and deletes user.
     *
     * @Route("/delete/{token}", name="app_delete")
     */
    public function deleteAction(Request $request, $token)
    {
        // JSON Web Token validation
        $jwt = $this->get('tangoman_jwt')->decode($token);

        $id       = $jwt->get('id');
        $username = $jwt->get('username');
        $action   = $jwt->get('action');

        // Display error message when token is invalid
        if ( !$jwt->isValid() || $action != "unsubscribe" ) {
            $this->get('session')->getFlashBag()->add('error', "Désolé <strong>$username</strong><br />".
                "Votre lien de sécurité n'est pas valide ou à expiré."
            );
            return $this->redirectToRoute('homepage');
        }

        // Find user
        $user = $this->get('em')->repository('AppBundle:User')->find($id);
        // When user doesn't exist
        if (!$user) {
            $this->get('session')->getFlashBag()->add('error',
                "Désolé <strong>$username</strong> ce compte a été supprimé."
            );
            return $this->redirectToRoute('homepage');
        } else {
            // Removes user
            $this->get('em')->remove($user);
            $this->get('em')->flush();

            // Sends success message
            $this->get('session')->getFlashBag()->add('success',
                "L'utilisateur <strong>$username</strong> à bien été supprimé."
            );

            // Disconnects user who deletes his own account
            if ( $user == $this->getUser() ) {
                $this->get('security.token_storage')->setToken(null);
                $request->getSession()->invalidate();
                return $this->redirectToRoute('homepage');
            }

            return $this->redirectToRoute('homepage');
        }
    }
}
