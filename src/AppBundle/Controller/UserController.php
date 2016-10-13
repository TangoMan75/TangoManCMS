<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package AppBundle\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all users.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $users = $this->get('em')->repository('AppBundle:User')->sorting(
            $request->query->getInt('page', 1),
            20,
            $request->query->get('order', 'username'),
            $request->query->get('way', 'ASC')
        );

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Exports user list in json format.
     *
     * @todo Firewall this route.
     *
     * @Route("/export-json", name="user_export_json")
     * @Method("GET")
     */
    public function exportJsonAction()
    {
        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);

        $response = serialize($users);

        return new JsonResponse($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_users.json"'
        ]);
    }

    /**
     * Exports user list in csv format.
     *
     * @todo Firewall this route.
     *
     * @Route("/export-csv", name="user_export_csv")
     * @Method("GET")
     */
    public function exportCSVAction()
    {
        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);

        $delimiter = ';';

        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, [
                'User Id',
                'Utilisateur',
                'Email',
                'Date de Création'
            ], $delimiter);

        foreach ($users as $user) {

            fputcsv($handle, [
                    $user->getId(),
                    $user->getusername(),
                    $user->getEmail(),
                    $user->getDateCreated()->format('d/m/Y H:i:s')
                ], $delimiter);

        }

        rewind($handle);
        $response = stream_get_contents($handle);
        fclose($handle);

        return new Response($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_users.csv"'
        ]);
    }

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

        // referrer url is cached into session when form is not yet submitted
        if ( !$form->isSubmitted() ) {

            $this->get('session')->set('callback_url', $request->headers->get('referer'));

        }

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
            return $this->redirect( $this->get('session')->get('callback_url') );

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

        // Deletes specified post
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
        return $this->redirect( $request->headers->get('referer') );
    }

    /**
     * Sets user roles.
     *
     * @Route("/add-role/{id}/{role}", requirements={"id": "\d+"}, name="user_add_role")
     */
    public function addRoleAction(Request $request, User $user, $role)
    {
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à modifier les roles utilisateur.");
            return $this->redirectToRoute('app_homepage');
        }

        $user->addRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "Le role <strong>&quot;$role&quot; à été attribué à &quot;{$user->getUsername()}&quot;</strong>.");

        // User is redirected to referrer page
        return $this->redirect( $request->headers->get('referer') );
    }

    /**
     * Removes user role.
     *
     * @todo Firewall this route instead of role checking.
     *
     * @Route("/remove-role/{id}/{role}", requirements={"id": "\d+"}, name="user_remove_role")
     */
    public function removeRoleAction(Request $request, User $user, $role)
    {
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à modifier les roles utilisateur.");
            return $this->redirectToRoute('app_homepage');
        }

        // Disconnects user who changes his own admin rights
        if ( $user == $this->getUser() ) {

            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_homepage');
        }

        $user->removeRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "Le role <strong>&quot;$role&quot; à été retiré à &quot;{$user->getUsername()}&quot;</strong>.");

        // User is redirected to referrer page
        return $this->redirect( $request->headers->get('referer') );
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
