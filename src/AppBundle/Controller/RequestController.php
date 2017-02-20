<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\EmailChangeType;
use AppBundle\Form\UserType;
use TangoMan\JWTBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/request")
 */
class RequestController extends Controller
{
    /**
     * Emails security token to given user.
     * @Route("/password_reset")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function passwordResetAction(Request $request)
    {
        // Create form
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

                return $this->redirectToRoute('app_request_passwordreset');
            }

            $email['title'] = 'Réinitialisation de mot de passe';
            $email['description'] = 'renouveler votre mot de passe';
            $email['btn'] = 'Réinitialiser mon mot de passe';
            $email['token'] = $this->genToken($user, 'password_reset');

            $this->sendToken($user, $email);
            $this->confirmMessage($user, $email);

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
     * Register new User.
     * @Route("/register")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // Instantiate new user entity
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Check form
        if ($form->isSubmitted() && $form->isValid()) {

            $email['token']['params'] = [$user];
            $email['title'] = 'Validation de votre inscription';
            $email['description'] = 'confirmer votre inscription';
            $email['btn'] = 'Valider mon compte';
            $email['token'] = $this->genToken($user, 'account_create');

            $this->sendToken($user, $email);
            $this->confirmMessage($user, $email);

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'user/register.html.twig',
            [
                'form_register' => $form->createView(),
            ]
        );
    }

    /**
     * Send email containing password reset security token.
     * @Route("/{id}/{action}", requirements={
     *     "id": "\d+",
     *     "action": "account_delete|email_change|newsletter_unsubscribe|password_change|password_reset|user_login|user_unsubscribe"
     * })
     *
     * @param Request  $request
     * @param   User   $user
     * @param   string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request, User $user, $action)
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

        $login = false;
        $params = [];

        switch ($action) {
            case 'account_delete':
                $email['title'] = 'Suppression de compte utilisateur';
                $email['description'] = 'confirmer votre désinscription';
                $email['btn'] = 'Supprimer mon compte';
                break;

            case 'email_change':
                $email['title'] = 'Changement d\'adresse email de contact';
                $email['description'] = 'enregistrer une nouvelle adresse email';
                $email['btn'] = 'Changer mon email';
                break;

            case 'password_change':
                $email['title'] = 'Changement de mot de passe';
                $email['description'] = 'modifier votre mot de passe';
                $email['btn'] = 'Changer mon mot de passe';
                break;

            case 'password_reset':
                $email['title'] = 'Réinitialisation de mot de passe';
                $email['description'] = 'renouveler votre mot de passe';
                $email['btn'] = 'Réinitialiser mon mot de passe';
                $login = true;
                break;

            case 'user_login':
                $email['title'] = 'Lien de connexion';
                $email['description'] = 'vous connecter à votre compte';
                $email['btn'] = 'Me connecter';
                $login = true;
                break;

            case 'test':
                $email['title'] = 'Test';
                $email['description'] = 'tester';
                $email['btn'] = 'Tester';
                $params = ['Param1', 'Param2', 'Param3'];
                break;

            case 'newsletter_unsubscribe':
                $email['title'] = 'Désabonnement à la liste';
                $email['description'] = 'vous désabonner à la liste';
                $email['btn'] = 'Me désabonner';
                $login = true;
                break;
        }

        $email['token'] = $this->genToken($user, $action, $params, $login);

        // Generates password reset and security warning
        $email['reset'] = $this->genToken($user, 'password_reset', [], true);

        $this->sendToken($user, $email);
        $this->confirmMessage($user, $email);

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * Change email
     *
     * @param   Request $request
     *
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
            'user/email_change.html.twig',
            [
                'user'            => $user,
                'formEmailChange' => $form->createView(),
            ]
        );
    }

    /**
     * Generates security token
     *
     * @param  User     $user      User
     * @param  string   $action    Action
     * @param  array    $params    Parameters
     * @param  boolean  $login     Login
     * @param  string   $validity
     *
     * @return  string  Token
     */
    public function genToken(User $user, $action, $params = [], $login = false, $validity = '+1 Day')
    {
        // Generates token
        $jwt = new JWT();
        $jwt->set('id', $user->getId())
            ->set('username', $user->getUsername())
            ->set('email', $user->getEmail())
            ->set('action', $action)
            ->set('params', $params)
            ->set('login', $login)
            ->setPeriod(new \DateTime(), new \DateTime($validity));

        return $this->get('tangoman_jwt')->encode($jwt);
    }

    /**
     * Sends token with swift mailer
     *
     * @param   User   $user
     * @param   string $token
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
                        'user'  => $user,
                        'token' => $token,
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }

    /**
     * Sends notification message
     *
     * @param   User   $user
     * @param   string $token
     */
    public function confirmMessage(User $user, $token)
    {
        $this->get('session')->getFlashBag()->add(
            'success',
            'Votre demande de <strong>'.mb_strtolower($token['title'], 'UTF-8').'</strong> a '.
            'bien été prise en compte.<br />Un lien de confirmation vous à été envoyé à <strong>'.$user->getEmail(
            ).'</strong>. '.
            '<br /> Vérifiez votre boîte email.'
        );
    }

}
