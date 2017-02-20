<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use AppBundle\Form\EmailChangeType;
use TangoMan\JWTBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @Route("/token")
 */
class TokenController extends Controller
{
    /**
     * Send email containing password reset security token.
     * @Route("/password-reset")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function passwordResetAction(Request $request)
    {
        $form = $this->createForm(\AppBundle\Form\EmailType::class);
        $form->handleRequest($request);

        // When form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['email' => $email]);

            // Send error message when user not found
            if (!$user) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Désolé, aucun utilisateur n\'est enregistré avec l\'email <strong>'.$email.'</strong>.'
                );

                return $this->redirectToRoute('app_token_passwordreset');
            }

            // Generate password reset token
            $token['token']       = $this->genToken($user, 'password-reset');
            $token['title']       = 'Réinitialisation de mot de passe';
            $token['description'] = 'renouveler votre mot de passe';
            $token['btn']         = 'Réinitialiser mon mot de passe';

            $this->sendToken($user, $token);
            $this->confirmMessage($user, $token);

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/reset.html.twig',
            [
                'formReset' => $form->createView(),
            ]
        );
    }

    /**
     * Send email containing password reset security token.
     * @Route("/request/{id}/{action}", requirements={"id": "\d+", "action": "account-delete|email-change|password-change|password-reset|user-login|user-unsubscribe"})
     *
     * @param Request $request
     * @param   User     $user
     * @param   string   $action
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function requestAction(Request $request, User $user, $action)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');
            return $this->redirectToRoute('app_login');
        }

        // Only user can send tokens to self
        if ($this->getUser() !== $user) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');
            return $this->redirectToRoute('homepage');
        }

        $token['token'] = $this->genToken($user, $action);

        // Generates password reset and security warning
        $token['reset'] = $this->genToken($user, 'password-reset');

        switch ($action) {
            case 'account-delete':
                $token['title']       = 'Suppression de compte utilisateur';
                $token['description'] = 'confirmer votre désinscription';
                $token['btn']         = 'Supprimer mon compte';
                break;

            case 'email-change':
                $token['title']       = 'Changement d\'adresse email de contact';
                $token['description'] = 'enregistrer une nouvelle adresse email';
                $token['btn']         = 'Changer mon email';
                break;

            case 'password-change':
                $token['title']       = 'Changement de mot de passe';
                $token['description'] = 'modifier votre mot de passe';
                $token['btn']         = 'Changer mon mot de passe';
                break;

            case 'password-reset':
                $token['title']       = 'Réinitialisation de mot de passe';
                $token['description'] = 'renouveler votre mot de passe';
                $token['btn']         = 'Réinitialiser mon mot de passe';
                break;

            case 'user-login':
                $token['title']       = 'Lien de connexion';
                $token['description'] = 'vous connecter à votre compte';
                $token['btn']         = 'Me connecter';
                break;

            case 'user-unsubscribe':
                $token['title']       = 'Désabonnement à la liste';
                $token['description'] = 'vous désabonner à la liste';
                $token['btn']         = 'Me désabonner';
                break;
        }

        $this->sendToken($user, $token);
        $this->confirmMessage($user, $token);

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * Security token front controller.
     * @Route("/{token}")
     *
     * @param Request $request
     * @param         $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function tokenAction(Request $request, $token)
    {
        // JSON Web Token validation
        $jwt      = $this->get('tangoman_jwt')->decode($token);
        $id       = $jwt->get('id');
        $username = $jwt->get('username');
        $email    = $jwt->get('email');
        $action   = $jwt->get('action');

        // Display error message when token is invalid
        if (!$jwt->isValid()) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé <strong>'.$username.'</strong><br />'.
                'Votre lien de sécurité n\'est pas valide ou à expiré.'
            );

            return $this->redirectToRoute('homepage');
        }

        // Find user
        $user = $this->get('em')->repository('AppBundle:User')->find($id);

        // When user doesn't exist
        if (!$user) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé <strong>'.$username.'</strong> ce compte a été supprimé.'
            );

            return $this->redirectToRoute('homepage');
        }

        // Check user data
        if ($user->getId() != $id || $user->getUsername() != $username || $user->getEmail() != $email) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé <strong>'.$username.'</strong><br />'.
                'Votre lien de sécurité n\'est pas valide.'
            );

            return $this->redirectToRoute('homepage');
        }

        // Starts user session
        $this->loginUser($user);

        switch ($action) {
            case 'account-delete':
                return $this->removeUser($request, $user);

            case 'email-change':
                return $this->changeEmail($request, $user);

            case 'password-change':
                $page['title'] = 'Changer de mot de passe';
                return $this->setPassword($request, $user, $page);

            case 'password-reset':
                $page['title'] = 'Réinitialisation de mot de passe';
                return $this->setPassword($request, $user, $page);

            case 'user-login':
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Bonjour <strong>'.$username.'</strong>! <br />Bienvenue.'
                );
                return $this->redirectToRoute('homepage');

            case 'user-unsubscribe':
                return $this->unsubscribe($request, $user);

            default:
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Désolé <strong>'.$username.'</strong>, une erreur s\'est produite.'
                );

                return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Set password
     * @param   Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function setPassword(Request $request, User $user, $page)
    {
        // Generate form
        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        // Check form validation
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            // Persist password
            $this->get('em')->save($user);

            $this->get('session')->getFlashBag()->add(
                'success',
                'Un nouveau mot de passe à bien été créé pour le compte <strong>'.$user->getUsername().'</strong>.'
            );

            // Start user session
            $sessionToken = new UsernamePasswordToken($user, null, 'database', $user->getRoles());
            $this->get('security.token_storage')->setToken($sessionToken);
            $this->get('session')->set('_security_main', serialize($sessionToken));

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/password.html.twig',
            [
                'page'         => $page,
                'formPassword' => $form->createView(),
            ]
        );
    }

    /**
     * Change email
     * @param   Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changeEmail(Request $request, User $user)
    {
        // Generate form
        $form = $this->createForm(EmailChangeType::class, $user);
        $form->handleRequest($request);

        // Check form validation
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist user
            $this->get('em')->save($user);

            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'email de contact à bien été changé pour le compte <strong>'.$user->getUsername().'</strong>.'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/email-change.html.twig',
            [
                'user'            => $user,
                'formEmailChange' => $form->createView(),
            ]
        );
    }

    /**
     * Removes user
     * @param   Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function removeUser(Request $request, User $user)
    {
        $username = $user->getUsername();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$username.'</strong><br />Il n\'est pas autorisé de supprimer un utilisateur avec des droit d\'administration.'
            );

            return $this->redirectToRoute('homepage');
        }

        // Removes user
        $this->get('em')->remove($user);
        $this->get('em')->flush();

        // Disconnects user
        if ($user == $this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
        }

        // Sends success message
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'utilisateur <strong>'.$username.'</strong> à bien été supprimé.'
        );

        return $this->redirectToRoute('homepage');
    }

    /**
     * Generates security token
     * @param   User    $user    User
     * @param   string  $action  Action
     * @return  string           Token
     */
    public function genToken(User $user, $action, $validity = '+1 Day') {
        // Generates token
        $jwt = new JWT();
        $jwt->set('id', $user->getId())
            ->set('username', $user->getUsername())
            ->set('email', $user->getEmail())
            ->set('action', $action)
            ->setPeriod(new \DateTime(), new \DateTime($validity));

        return $this->get('tangoman_jwt')->encode($jwt);
    }

    /**
     * Start user session
     * @param   User    $user  [description]
     */
    public function loginUser(User $user) {
        // Start user session
        $sessionToken = new UsernamePasswordToken($user, null, 'database', $user->getRoles());
        $this->get('security.token_storage')->setToken($sessionToken);
        $this->get('session')->set('_security_main', serialize($sessionToken));
    }

    /**
     * Sends token with swift mailer
     * @param   User    $user
     * @param   string  $token
     */
    public function sendToken(User $user, $token)
    {
        // Sends email to user
        $message = \Swift_Message::newInstance()
            ->setSubject($this->getParameter('site_name').' | '.$token['title'])
            ->setFrom($this->getParameter('mailer_from'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'email/token.html.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }

    /**
     * Sends notification message
     * @param   User    $user
     * @param   string  $token
     */
    public function confirmMessage(User $user, $token)
    {
        $this->get('session')->getFlashBag()->add(
            'success',
            'Votre demande de <strong>'.mb_strtolower($token['title'], 'UTF-8').'</strong> a '.
            'bien été prise en compte.<br />Un lien de confirmation vous à été envoyé à <strong>'.$user->getEmail().'</strong>. '.
            '<br /> Vérifiez votre boîte email.'
        );
    }

}
