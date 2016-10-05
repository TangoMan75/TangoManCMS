<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package AppBundle\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'asc']);

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('user/edit.html.twig', [

        ]);
    }

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        $user = new User;

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {

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

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('user/register.html.twig', [
            'form_register' => $form->createView()
        ]);
    }

    /**
     * Finds and deletes a User entity.
     *
     * @Route("/delete/{id}", name="user_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, User $user)
    {
        $this->get('em')->remove($user);
        $this->get('em')->flush();

        $this->get('session')->getFlashBag()->add('success', "L'utilisateur <strong>&quot;{$user->getUsername()}&quot;</strong> à bien été supprimé.");

        // Disconnects user
        if ($user == $this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_homepage');
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Finds and displays a User entity.
     * @Route("/{username}", name="user_show")
     */
    public function showAction(Request $request, $username)
    {
        $user = $this->get('em')->repository('AppBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas.");
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

}
