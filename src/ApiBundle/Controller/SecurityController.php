<?php
/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ApiBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use TangoMan\JWTBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 * api follows jsend standard.
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/api")
 */
class SecurityController extends Controller
{

    /**
     * Register new user.
     * @Route("/register")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        // Instantiate new user entity
        $user = new User;

        $msg['title'] = 'Création de compte';
        $msg['token'] = $this->genToken($user, 'account_create');

        $this->sendEmail($user, $msg, 'email/user-register.html.twig');
        $this->confirmMessage($user, $msg);

        return new JsonResponse(
            [
                'status' => 'success',
                'data'   => null,
            ]
        );
    }

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
            $em    = $this->get('doctrine')->getManager();
            $user  = $em->getRepository('AppBundle:User')->findOneBy(
                ['email' => $email]
            );

            // Send error message when user not found
            if ( ! $user) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Désolé, aucun utilisateur n\'est enregistré avec l\'email <strong>'
                    .$email.'</strong>.'
                );

                return $this->redirectToRoute('app_security_passwordreset');
            }

            $msg['title']       = 'Réinitialisation de mot de passe';
            $msg['description'] = 'renouveler votre mot de passe';
            $msg['btn']         = 'Réinitialiser mon mot de passe';
            $msg['token']       = $this->genToken($user, 'password_reset');

            $this->sendEmail($user, $msg, 'email/token.html.twig');
            $this->confirmMessage($user, $msg);

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
     * Change email
     * @Route("/security/email_change/{id}", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     *
     * @param   Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function emailChange(Request $request, User $user)
    {
        // Only user can send tokens to self
        if ($this->getUser() !== $user) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Vous n\'êtes pas autorisé à réaliser cette action.'
            );

            return $this->redirectToRoute('homepage');
        }

        // Cache old user
        $oldUser = clone $user;

        // Generate form
        $form = $this->createForm(EmailChangeType::class, $user);
        $form->handleRequest($request);

        // Check form validation
        if ($form->isSubmitted() && $form->isValid()) {

            // When emails are identical
            if ($oldUser->getEmail() == $user->getEmail()) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'L\'email saisi est identique au précedent'
                );

                // User is redirected to referrer page
                return $this->redirect($request->get('callback'));
            }

            // Persist user
            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush();

            $recoveryMsg['token']    = $this->genToken(
                $oldUser,
                'account_recovery',
                [],
                true,
                '+1 Week'
            );
            $recoveryMsg['title']    = 'Récupération de compte';
            $recoveryMsg['newEmail'] = $user->getEmail();
            $this->sendEmail(
                $oldUser,
                $recoveryMsg,
                'email/account-recovery.html.twig'
            );

            $changeMsg['title'] = 'Changement d\'adresse email';
            $this->sendEmail($user, $changeMsg, 'email/email-change.html.twig');

            $this->get('session')->getFlashBag()->add(
                'success',
                'Votre demande de <strong>changement d\'adresse email</strong> a '
                .
                'bien été prise en compte.<br />'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
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
     * Send email containing security token.
     * @Route("/security/{action}/{id}", requirements={
     *     "action": "account_delete|password_change|user_login",
     *     "id": "\d+"
     * })
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     *
     * @param Request  $request
     * @param   User   $user
     * @param   string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request, $action, User $user)
    {
        // Only user can send tokens to self
        if ($this->getUser() !== $user) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Vous n\'êtes pas autorisé à réaliser cette action.'
            );

            return $this->redirectToRoute('homepage');
        }

        $login  = false;
        $params = [];

        switch ($action) {
            case 'account_delete':
                $msg['title']       = 'Suppression de compte utilisateur';
                $msg['description'] = 'confirmer votre désinscription';
                $msg['btn']         = 'Supprimer mon compte';
                break;

            case 'password_change':
                $msg['title']       = 'Changement de mot de passe';
                $msg['description'] = 'modifier votre mot de passe';
                $msg['btn']         = 'Changer mon mot de passe';
                break;

            case 'user_login':
                $msg['title']       = 'Lien de connexion';
                $msg['description'] = 'vous connecter à votre compte';
                $msg['btn']         = 'Me connecter';
                $login              = true;
                break;
        }

        $msg['token'] = $this->genToken($user, $action, $params, $login);

        // Generates password reset and security warning
        $msg['reset'] = $this->genToken($user, 'password_reset', [], true);

        $this->sendEmail($user, $msg, 'email/token.html.twig');
        $this->confirmMessage($user, $msg);

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * Generates security token
     *
     * @param  User    $user   User
     * @param  string  $action Action
     * @param  array   $params Parameters
     * @param  boolean $login  Login
     * @param  string  $validity
     *
     * @return  string  Token
     */
    public function genToken(
        User $user,
        $action,
        $params = [],
        $login = false,
        $validity = '+1 Day'
    ) {
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
     * @param   User  $user
     * @param   array $msg
     */
    public function sendEmail(User $user, $msg, $view)
    {
        // Sends email to user
        $message = \Swift_Message::newInstance()
                                 ->setSubject(
                                     $this->getParameter('site_name').' | '
                                     .$msg['title']
                                 )
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

    /**
     * Sends notification message
     *
     * @param   User  $user
     * @param   array $msg
     */
    public function confirmMessage(User $user, $msg)
    {
        $this->get('session')->getFlashBag()->add(
            'success',
            'Votre demande de <strong>'.mb_strtolower(
                $msg['title'],
                'UTF-8'
            ).'</strong> a bien été prise en compte.<br />'.
            'Un lien de confirmation sécurisé vous à été envoyé à <strong>'
            .$user->getEmail().'</strong>.<br/>'.
            'Vérifiez votre boîte email.<br/>'.
            'Si vous ne reçevez pas d\'email, il est possible qu\'il soit dans votre dossier de spam.'
        );
    }

}
