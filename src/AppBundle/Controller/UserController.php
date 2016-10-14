<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\AvatarType;
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
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

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
            $this->get('session')->getFlashBag()->add('success', "Merci <strong>$username</strong>, votre demande d'inscription a bien été prise en compte.<br />Un lien de comfirmation vous à été envoyé à <strong>$email</strong>. <br /> Vérifiez votre boîte email.");

            // User is redirected to referrer page
            return $this->redirect( $request->get('callback') );

        }

        return $this->render('user/register.html.twig', [
            'form_register' => $form->createView()
        ]);
    }

    /**
     * Finds and deletes a User entity.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="user_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, User $user)
    {
        if ( $this->getUser() !== $user && !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à supprimer cet utilisateur.");
            return $this->redirectToRoute('app_homepage');

        }

        // Deletes specified user
        $this->get('em')->remove($user);
        $this->get('em')->flush();
        $this->get('session')->getFlashBag()->add('success', "L'utilisateur <strong>&quot;{$user->getUsername()}&quot;</strong> à bien été supprimé.");

        // Disconnects user
        if ($user == $this->getUser()) {

            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_homepage');

        }

        // User is redirected to referrer page
        return $this->redirect( $request->get('callback') );
    }

    /**
     * Finds and edits a User entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="user_edit")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function editAction(Request $request, User $user)
    {
        if ( $this->getUser() !== $user && !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) )
        {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à modifier cet utilisateur.");
            return $this->redirectToRoute('app_homepage');

        }

        dump($user);

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
