<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminNewUserType;
use AppBundle\Form\AdminEditUserType;
use AppBundle\Form\FileUploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * Lists all users.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated user list
        $em = $this->get('doctrine')->getManager();
        $users = $em->getRepository('AppBundle:User')->sortedSearchPaged($request->query, 20);

        return $this->render(
            'admin/user/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'users'       => $users,
                'page'        => $request->query->get('page', 1),
                'order'       => $request->query->get('order'),
                'way'         => $request->query->get('way', 'ASC'),
                'username'    => $request->query->get('username'),
                'email'       => $request->query->get('email'),
                'role'        => $request->query->get('role'),
            ]
        );
    }

    /**
     * @Route("/import")
     */
    public function importAction(Request $request)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $file = $request->files->get('file_upload')['file'];

            if (!$file->isValid()) {
                // Upload success check
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Une erreur s\'est produite lors du transfert.<br/>Veuillez réessayer.'
                );

                return $this->redirectToRoute('app_admin_user_import');
            }

            return $this->importCSV($file);
        }

        return $this->render(
            'admin/user/import.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/export")
     */
    public function exportAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render(
            'admin/user/export.html.twig',
            [
                'currentUser' => $this->getUser(),
                'users'       => $users,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(AdminNewUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            // Persists new user
            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'L\'utilisateur a bien été ajouté.');

            // User is redirected to referrer page
            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render(
            'admin/user/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(AdminEditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash password
            $encoder = $this->get('security.password_encoder');
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            // Persists edited user
            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add('success', 'L\'utilisateur a bien été modifié.');

            return $this->redirectToRoute(
                'app_admin_user_index',
                [
                    'page'     => $request->query->get('page', 1),
                    'order'    => $request->query->get('order'),
                    'way'      => $request->query->get('way', 'ASC'),
                    'username' => $request->query->get('username'),
                    'email'    => $request->query->get('email'),
                    'role'     => $request->query->get('role'),
                ]
            );
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'user'        => $user,
            ]
        );
    }

    /**
     * Finds and deletes a User entity.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, User $user)
    {
        $admin = $this->getUser();

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$admin->getUsername().'</strong><br />'.
                'Il n\'est pas autorisé de supprimer un utilisateur ayant des droit d\'administration.'
            );

            return $this->redirectToRoute(
                'app_admin_user_index',
                [
                    'page'     => $request->query->get('page', 1),
                    'order'    => $request->query->get('order'),
                    'way'      => $request->query->get('way', 'ASC'),
                    'username' => $request->query->get('username'),
                    'email'    => $request->query->get('email'),
                    'role'     => $request->query->get('role'),
                ]
            );
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($user);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'utilisateur <strong>&quot;'.$user->getUsername().'&quot;</strong> à bien été supprimé.'
        );

        // Admin is redirected to referrer page
        return $this->redirectToRoute(
            'app_admin_user_index',
            [
                'page'     => $request->query->get('page', 1),
                'order'    => $request->query->get('order'),
                'way'      => $request->query->get('way', 'ASC'),
                'username' => $request->query->get('username'),
                'email'    => $request->query->get('email'),
                'role'     => $request->query->get('role'),
            ]
        );
    }

    /**
     * Sets user roles.
     * @Route("/add-role/{id}/{add}", requirements={"id": "\d+"})
     */
    public function addRoleAction(Request $request, User $user, $add)
    {
        // Only admin manage user roles
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à modifier les roles utilisateur.'
            );

            return $this->redirectToRoute('homepage');
        }
        $user->addRole($add);
        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$add.'&quot; à été attribué à '.
            '&quot;'.$user->getUsername().'&quot;</strong>.'
        );

        // User is redirected to referrer page
        return $this->redirectToRoute(
            'app_admin_user_index',
            [
                'page'     => $request->query->get('page', 1),
                'order'    => $request->query->get('order'),
                'way'      => $request->query->get('way', 'ASC'),
                'username' => $request->query->get('username'),
                'email'    => $request->query->get('email'),
                'role'     => $request->query->get('role'),
            ]
        );
    }

    /**
     * Removes user role.
     * @Route("/remove-role/{id}/{remove}", requirements={"id": "\d+"})
     */
    public function removeRoleAction(Request $request, User $user, $remove)
    {
        // Only admin manage user roles
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à modifier les roles utilisateur.'
            );

            return $this->redirectToRoute('homepage');
        }

        // Super admins can't change own role
        if ($user == $this->getUser() && $remove == 'ROLE_SUPER_ADMIN') {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$user->getUsername().'</strong><br />'.
                'Vous n\'êtes pas autorisé à supprimer vos propres droit d\'administration.'
            );

            return $this->redirectToRoute(
                'app_admin_user_index',
                [
                    'page'     => $request->query->get('page', 1),
                    'order'    => $request->query->get('order'),
                    'way'      => $request->query->get('way', 'ASC'),
                    'username' => $request->query->get('username'),
                    'email'    => $request->query->get('email'),
                    'role'     => $request->query->get('role'),
                ]
            );
        }

        $user->removeRole($remove);
        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$remove.'&quot; à été retiré à '.
            '&quot;'.$user->getUsername().'&quot;</strong>.'
        );

        // User is redirected to referrer page
        return $this->redirectToRoute(
            'app_admin_user_index',
            [
                'page'     => $request->query->get('page', 1),
                'order'    => $request->query->get('order'),
                'way'      => $request->query->get('way', 'ASC'),
                'username' => $request->query->get('username'),
                'email'    => $request->query->get('email'),
                'role'     => $request->query->get('role'),
            ]
        );
    }

    /**
     * @param $file
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importCSV($file)
    {
        // Security checks
        $validExtensions = ['csv', 'tsv'];
        $clientExtension = $file->getClientOriginalExtension();

        if ($file->getClientMimeType() !== 'application/vnd.ms-excel' &&
            !in_array($clientExtension, $validExtensions)
        ) {

            $this->get('session')->getFlashBag()->add('error', 'Ce format du fichier n\'est pas supporté.');

            return $this->redirectToRoute('app_admin_user_import');
        } else {
            // Get CSV reader service
            $reader = $this->get('services.csv_reader');
            $counter = 0;
            $dupes = 0;
            // File check
            if (is_file($file)) {
                // Init reader service
                $reader->init($file, 0, ";");
                // Load user entity
                $em = $this->get('doctrine')->getManager();
                $users = $em->getRepository('AppBundle:User');
                while (false !== ($line = $reader->readLine())) {
                    // Check if user with same email already exist
                    $user = $users->findOneByEmail($line->get('email'));
                    // When not found persist new user
                    if (!$user) {
                        $counter++;
                        $user = new User();
                        $user->setUsername($line->get('username'))
                            ->setSlug($line->get('slug'))
                            ->setBio($line->get('bio'))
                            ->setEmail($line->get('email'))
                            ->setPassword($line->get('password'))
                            ->setAvatar($line->get('avatar'))
                            ->setRoles(explode(",", $line->get('roles')))
                            ->setDateCreated(date_create_from_format('Y/m/d H:i:s', $line->get('date_created')));

                        $em->persist($user);
                        $em->flush();
                    } else {
                        $dupes++;
                    }
                }
            }

            if ($counter > 1) {
                $success = "$counter comptes utilisateur ont été importés.";
            } else {
                $success = "Aucun compte utilisateur n'a été importé.";
            }

            $this->get('session')->getFlashBag()->add('success', $success);
        }

        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * Exports user list in csv format.
     * @Route("/export-csv")
     */
    public function exportCSVAction()
    {
        $em = $this->get('doctrine')->getManager();
        $users = $em->getRepository('AppBundle:User')->findBy([], ['username' => 'ASC']);

        $delimiter = ";";
        $handle = fopen('php://memory', 'r+');

        fputcsv(
            $handle,
            [
                'id',
                'slug',
                'username',
                'email',
                'avatar',
                'bio',
                'password',
                'roles',
                'date_created',
            ],
            $delimiter
        );

        foreach ($users as $user) {
            fputcsv(
                $handle,
                [
                    $user->getId(),
                    $user->getSlug(),
                    $user->getUsername(),
                    $user->getEmail(),
                    $user->getAvatar(),
                    $user->getBio(),
                    $user->getPassword(),
                    implode(",", $user->getRoles()),
                    $user->getDateCreated()->format('Y/m/d H:i:s'),
                ],
                $delimiter
            );
        }

        rewind($handle);
        $response = stream_get_contents($handle);
        fclose($handle);

        return new Response(
            $response, 200, [
                'Content-Type'        => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="users.csv"',
            ]
        );
    }
}
