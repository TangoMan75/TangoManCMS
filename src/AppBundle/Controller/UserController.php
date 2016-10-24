<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\AvatarType;
use AppBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * @package AppBundle\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Register new User.
     *
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        // Instantiate new user entity
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Check form
        if ( $form->isSubmitted() && $form->isValid() ) {
            $username = $user->getUsername();
            $email    = $user->getEmail();

            // Generate JSON Web Token
            $jwt = new JWT();
            $jwt->set('username', $username);
            $jwt->set('email', $email);
            $jwt->set('action', 'password');
            $jwt->setPeriod(new \DateTime(), new \DateTime('+1 days'));
            $token = $this->get('jwt')->encode($jwt);

            // Create email containing token for validation
            $message = \Swift_Message::newInstance()
                ->setSubject("Livre D'Or | Confirmation d'inscription.")
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('email/validation.html.twig', [
                        'user' => $user,
                        'token' => $token
                    ]),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            // Send flash notification
            $this->get('session')->getFlashBag()->add('success',
                "Votre demande d'inscription a bien été prise en compte. ".
                "<br />Un lien de confirmation vous à été envoyé à <strong>$email</strong>. <br />".
                "Vérifiez votre boîte email.");

            // User is redirected to referrer page
            return $this->redirect( $request->get('callback') );
        }

        return $this->render('user/register.html.twig', [
            'form_register' => $form->createView()
        ]);
    }

    /**
     * Edit User.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="user_edit")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function editAction(Request $request, User $user)
    {
        if ( $this->getUser() !== $user && !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à modifier cet utilisateur.");
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(AvatarType::class, $user);
        $form->handleRequest($request);
        $formImage = $form->createView();

        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->get('em')->save($user);
            $this->get('session')->getFlashBag()->add('success', 'Votre avatar a bien été enregistré.');

            // User is redirected to referrer page
            return $this->redirect( $request->get('callback') );
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form_avatar' => $formImage
        ]);
    }

    /**
     * Unsubscribe User.
     *
     * @Route("/unsubscribe/{id}", requirements={"id": "\d+"}, name="user_unsubscribe")
     * @param Request $request
     * @param User $user
     */
    public function unsubscribeAction(Request $request, User $user)
    {
        // Check if is account owner
        if ( !$user == $this->getUser() ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        } else {
            $username = $user->getUsername();
            $email = $user->getEmail();

            // Generate JSON Web Token
            $jwt = new JWT();
            $jwt->set('id', $user->getId());
            $jwt->set('email', $email);
            $jwt->set('action', 'unsubscribe');
            $jwt->setPeriod(new \DateTime(), new \DateTime('+1 days'));
            $token = $this->get('jwt')->encode($jwt);

            // Create email containing token for validation
            $message = \Swift_Message::newInstance()
                ->setSubject("Livre D'Or | Confirmation de désinscription.")
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('email/unsubscribe.html.twig', [
                        'user' => $user,
                        'token' => $token
                    ]),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            // Send flash notification
            $this->get('session')->getFlashBag()->add('success',
                "La demande de désactivation de votre compte &quot;<strong>$username</strong>&quot; à été enregistrée.<br />".
                "Un lien de confirmation vous à été envoyé à <strong>$email</strong>. <br />".
                "Vérifiez votre boîte email.");

            // User is redirected to referrer page
            return $this->redirect( $request->get('callback') );
        }
    }

    /**
     * Delete User entity.
     *
     * @Route("/delete/{token}", name="user_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $token)
    {
        // JSON Web Token validation
        $jwt = $this->get('jwt')->decode($token);
        $id     = $jwt->get('id');
        $email  = $jwt->get('email');
        $action = $jwt->get('action');

        // Instantiate user entity
        $user = $this->get('em')->repository('AppBundle:User')->find($id);

        // Displays error message when token is invalid
        if ( !$jwt->isValid() || $action !== "unsubscribe" || $email !== $user->getEmail() ) {
            $this->get('session')->getFlashBag()->add('error', "Désolé <strong>$username</strong><br />".
                "Votre lien de sécurité n'est pas valide ou à expiré.<br />".
                "Vous devez recommencer le procéssus d'inscription."
            );
            return $this->redirectToRoute('app_homepage');
        }

        // Deletes specified user
        $this->get('em')->remove($user);
        $this->get('em')->flush();

        // Disconnects user
        if ($user == $this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
        }

        // Send flash notification
        $this->get('session')->getFlashBag()->add('success',
            "Au revoir <strong>&quot;{$user->getUsername()}&quot;</strong><br />".
            "Votre compte a été supprimé.");

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * Display User entity.
     *
     * @Route("/{username}", name="user_show")
     */
    public function showAction(Request $request, $username)
    {
        $user = $this->get('em')->repository('AppBundle:User')->findOneByUsername($username);

        if ( !$user ) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas.");
        }

        $listPost = $this->get('em')->repository('AppBundle:Post')->findByUserPaged($user, $request->query->getInt('page', 1), 5);

        return $this->render('user/show.html.twig', [
            'user'      => $user,
            'list_post' => $listPost
        ]);
    }

}
