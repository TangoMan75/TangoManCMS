<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use AppBundle\Form\EmailChangeType;
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
     * Security token front controller.
     * @Route("/{token}")
     *
     * @param Request $request
     * @param         $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function callAction(Request $request, $token)
    {
        // JSON Web Token validation
        $jwt = $this->get('tangoman_jwt')->decode($token);
        $id = $jwt->get('id');
        $username = $jwt->get('username');
        $email = $jwt->get('email');
        $action = $jwt->get('action');
        $params = $jwt->get('params');
        $login = $jwt->get('login');

        // Display error message when token is invalid
        if (!$jwt->isValid()) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé <strong>'.$username.'</strong><br />'.
                'Votre lien de sécurité n\'est pas valide ou à expiré.'
            );

            return $this->redirectToRoute('homepage');
        }

        // When creating new account
        if ($action == 'account_create') {
            return $this->account_create($request, $username, $email);
        }

        // Find user
        $user = $this->get('em')->repository('AppBundle:User')->find($id);

        // When user doesn't exist
        if (!$user) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé <strong>'.$username.'</strong> ce compte n\'existe pas.'
            );

            return $this->redirectToRoute('homepage');
        }

        // When creating new account
        if ($action == 'account_recovery') {
            return $this->account_recovery($request, $user, $username, $email);
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
        if ($login) {
            $this->loginUser($user);
        }

        // Adds parameters
        array_unshift($params, $user);
        array_unshift($params, $request);

        // Calls requested method
        return call_user_func_array([$this, $action], $params);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function account_create(Request $request, $username, $email)
    {
        // Checks if user already exists
        $user = $this->get('em')->repository('AppBundle:User')->findOneBy(['email' => $email]);

        if ($user) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Un utilisateur est déjà enregistré avec l\'email: <strong>'.$email.'</strong>.<br/>'
            );

            return $this->redirectToRoute('homepage');
        }

        // Instantiate new user entity
        $user = new User;
        $user->setUsername($username);
        $user->setEmail($email);

        // generates password form
        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        // Check form
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            // Persists new user
            $this->get('em')->save($user);

            // Login user
            $this->loginUser($user);
            $this->user_login($request, $user);

            // Send welcome message
            $msg['title'] = 'Bienvenue';
            $this->sendEmail($user, $msg, 'email/user-welcome.html.twig');

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/password-create.html.twig',
            [
                'formPassword' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function password_reset(Request $request, User $user)
    {
        // generates password form
        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        // Check form
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            // Persists new password
            $this->get('em')->save($user);

            // Login user
            $this->loginUser($user);
            $this->user_login($request, $user);

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/password-reset.html.twig',
            [
                'formPassword' => $form->createView(),
            ]
        );
    }

    /**
     * Change email
     *
     * @param   Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function email_change_confirm(Request $request, User $user)
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
     * Account recovery
     *
     * @param Request $request
     * @param User    $user
     * @param string  $username
     * @param string  $email
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function account_recovery(Request $request, User $user, $username, $email)
    {
        // Restores username
        $user->setUsername($username);

        // Restores email
        $user->setEmail($email);

        // Persists recovered user
        $this->get('em')->save($user);

        // generates password form
        $form = $this->createForm(PwdType::class, $user);
        $form->handleRequest($request);

        // Check form
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            // Persists new password
            $this->get('em')->save($user);

            // Login user
            $this->loginUser($user);
            $this->user_login($request, $user);

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'user/account-recovery.html.twig',
            [
                'user'         => $user,
                'formPassword' => $form->createView(),
            ]
        );
    }

    /**
     * Removes user
     *
     * @param   Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function account_delete(Request $request, User $user)
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
     * Set password
     *
     * @param   Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function password_change(Request $request, User $user)
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

            // Persist new password
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
            'user/password-change.html.twig',
            [
                'formPassword' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function user_login(Request $request, User $user)
    {
        $this->get('session')->getFlashBag()->add(
            'success',
            'Bonjour <strong>'.$user->getUsername().'</strong>! <br />Bienvenue.'
        );

        return $this->redirectToRoute('homepage');
    }

    /**
     * Start user session
     *
     * @param   User $user [description]
     */
    public function loginUser(User $user)
    {
        // Start user session
        $sessionToken = new UsernamePasswordToken($user, null, 'database', $user->getRoles());
        $this->get('security.token_storage')->setToken($sessionToken);
        $this->get('session')->set('_security_main', serialize($sessionToken));
    }

    /**
     * Send email with swift mailer
     *
     * @param   User   $user
     * @param   string $msg
     */
    public function sendEmail(User $user, $msg, $view)
    {
        // Sends email to user
        $message = \Swift_Message::newInstance()
            ->setSubject($this->getParameter('site_name').' | '.$msg['title'])
            ->setFrom($this->getParameter('mailer_from'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    $view,
                    [
                        'user' => $user,
                        'msg'  => $msg,
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }
}
