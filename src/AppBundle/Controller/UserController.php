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
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findBy([], ['username' => 'asc']);

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

//            $encoder = $this->get('security.password_encoder');
//            $encoded = $encoder->encodePassword($user, $user->getPassword());
//            $user->setPassword($encoded);

            $username = $user->getUsername();
            $email    = $user->getEmail();

            // Generates token from username and unix time
            $user->setToken(md5(time().$username));

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', "Merci $username, votre demande d'inscription a bien été prise en compte.<br />Un lien de comfirmation vous à été envoyé à $email. <br /> Vérifiez votre boîte email.");

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('user/register.html.twig', [
            'form_register' => $form->createView(),
        ]);
    }

    /**
     * Confirms user email.
     *
     * @Route("/confirm/{token}", name="user_confirm")
     */
    public function confirmAction(Request $request, User $user)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneByToken($token);

    }

    /**
     * Finds and deletes a User entity.
     *
     * @Route("/delete/{id}", name="user_delete")
     * @Method("GET")
     */
    public function deleteUser (Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', "L'utilisateur <strong>" . $user->getUsername() . '</strong> à bien été supprimé.');

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
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas.");
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
