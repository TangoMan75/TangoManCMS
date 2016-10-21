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
     * Registers new user.
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

            $user->setToken($token);

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
     * Finds and edits a User entity.
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
     * Unsubscribe user.
     *
     * @Route("/unsubscribe", name="user_unsubscribe")
     * @param Request $request
     * @param User $id
     */
    public function unsubscribeAction(Request $request, User $id)
    {
        $username = $user->getUsername();
        $email    = $user->getEmail();

        // Generate JSON Web Token
        $jwt = new JWT();
        $jwt->set('username', $username);
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
                $this->renderView('email/validation.html.twig', [
                    'user' => $user,
                    'token' => $token
                ]),
                'text/html'
            )
        ;
            $this->get('mailer')->send($message);
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
     * Finds and displays a User entity.
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
