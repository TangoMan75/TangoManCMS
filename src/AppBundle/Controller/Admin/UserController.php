<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\Admin\AdminNewUserType;
use AppBundle\Form\Admin\AdminEditUserType;
use AppBundle\Form\FileUploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TangoMan\CSVExportHelper\CSVExportHelper;

/**
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    use CSVExportHelper;

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated user list
        $em = $this->get('doctrine')->getManager();
        $users = $em->getRepository('AppBundle:User')->findByQuery($request->query);

        $menu = '{
            "items": [
                {
                    "label": "Tableau de bord",
                    "route": "app_admin_admin_index",
                    "icon": "glyphicon glyphicon-dashboard"
                },
                {
                    "label": "Sites",
                    "route": "app_admin_site_index",
                    "icon": "glyphicon glyphicon-home"
                },
                {
                    "label": "Pages",
                    "route": "app_admin_page_index",
                    "icon": "glyphicon glyphicon-file"
                },
                {
                    "label": "Sections",
                    "route": "app_admin_section_index",
                    "icon": "glyphicon glyphicon-list-alt"
                },
                {
                    "label": "Galeries",
                    "route": "app_admin_gallery_index",
                    "icon": "glyphicon glyphicon-picture"
                },
                {
                    "divider": true
                },
                {
                    "label": "Articles",
                    "route": "app_admin_post_index",
                    "icon": "glyphicon glyphicon-text-color"
                },
                {
                    "label": "Medias",
                    "route": "app_admin_media_index",
                    "icon": "glyphicon glyphicon-music"
                },
                {
                    "label": "Commentaires",
                    "route": "app_admin_comment_index",
                    "icon": "glyphicon glyphicon-comment"
                },
                {
                    "label": "Étiquettes",
                    "route": "app_admin_tag_index",
                    "icon": "glyphicon glyphicon-tag"
                },
                {
                    "divider": true
                },
                {
                    "label": "Utilisateurs",
                    "route": "app_admin_user_index",
                    "icon": "glyphicon glyphicon-user"
                },
                {
                    "label": "Rôles",
                    "route": "app_admin_role_index",
                    "icon": "glyphicon glyphicon-king"
                },
                {
                    "label": "Privilèges",
                    "route": "app_admin_privilege_index",
                    "icon": "glyphicon glyphicon-thumbs-up"
                }
            ]
        }';

        $form = '{
            "inputs": [
                {
                    "type": "number",
                    "name": "e-id",
                    "icon": "fa fa-hashtag",
                    "label": "Id"
                },
                {
                    "type": "text",
                    "icon": "glyphicon glyphicon-user",
                    "name": "user-username",
                    "label": "Utilisateur"
                },
                {
                    "type": "text",
                    "icon": "glyphicon glyphicon-envelope",
                    "name": "user-email",
                    "label": "Email"
                },
                {
                    "type": "select",
                    "name": "n-password",
                    "icon": "glyphicon glyphicon-ok",
                    "label": "Status",
                    "options": [
                        {
                            "name": "Tous",
                            "value": null
                        },
                        {
                            "name": "Actif",
                            "value": "true"
                        },
                        {
                            "name": "Inactif",
                            "value": "false"
                        }
                    ]
                },
                {
                    "type": "select",
                    "name": "roles-type",
                    "label": "Role",
                    "icon": "glyphicon glyphicon-king",
                    "options": [
                        {
                            "name": "Tous",
                            "value": null
                        },
                        {
                            "name": "Super Admin",
                            "value": "ROLE_SUPER_ADMIN"
                        },
                        {
                            "name": "Admin",
                            "value": "ROLE_ADMIN"
                        },
                        {
                            "name": "Super Utilisateur",
                            "value": "ROLE_SUPER_USER"
                        },
                        {
                            "name": "Utilisateur",
                            "value": "ROLE_USER"
                        }
                    ]
                },
                {
                    "type": "submit",
                    "label": "Filtrer",
                    "icon": "glyphicon glyphicon-search"
                },
                {
                    "type": "reset",
                    "label": "Effacer",
                    "icon": "glyphicon glyphicon-remove"
                }
            ]
        }';

        $order = '{
            "fields": [
                {
                    "name": "username",
                    "label": "Utilisateur",
                    "route": "app_admin_user_index",
                    "colspan": 2
                },
                {
                    "name": "email",
                    "label": "Email",
                    "route": "app_admin_user_index"
                },
                {
                    "name": "c-posts",
                    "label": "Articles",
                    "route": "app_admin_user_index"
                },
                {
                    "name": "c-posts",
                    "label": "Médias",
                    "route": "app_admin_user_index"
                },
                {
                    "name": "c-comments",
                    "label": "Commentaires",
                    "route": "app_admin_user_index"
                },
                {
                    "name": "created",
                    "label": "Création",
                    "route": "app_admin_user_index"
                },
                {
                    "name": "password",
                    "label": "Actif",
                    "route": "app_admin_user_index"
                },
                {
                    "label": "Actions",
                    "colspan": 3
                }
            ]
        }';

        return $this->render(
            'admin/user/index.html.twig',
            [
                'menu' => $menu,
                'form' => $form,
                'order' => $order,
                'users' => $users,
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

            $this->get('session')->getFlashBag()->add('success', 'L\'utilisateur '.$user.' a bien été ajouté.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/user/new.html.twig',
            [
                'form' => $form->createView(),
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

            $this->get('session')->getFlashBag()->add('success', 'L\'utilisateur <strong>&quot;'.$user.'&quot;</strong> a bien été modifié.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, User $user)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Il n\'est pas autorisé de supprimer un utilisateur ayant des droit d\'administration.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($user);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'utilisateur <strong>&quot;'.$user.'&quot;</strong> a bien été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
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

        $em = $this->get('doctrine')->getManager();
        $role = $em->getRepository('AppBundle:Role')->findOneBy(['type' => $add]);
        $user->addRole($role);
        $em->persist($user);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$add.'&quot; a été attribué à '.
            '&quot;'.$user.'&quot;</strong>.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
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
                'Désolé, <strong>'.$user.'</strong><br />'.
                'Vous n\'êtes pas autorisé à supprimer vos propres droit d\'administration.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        $em = $this->get('doctrine')->getManager();
        $role = $em->getRepository('AppBundle:Role')->findOneBy(['type' => $remove]);
        $user->removeRole($role);
        $em->persist($user);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$remove.'&quot; a été retiré à '.
            '&quot;'.$user.'&quot;</strong>.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/export")
     */
    public function exportAction()
    {
        $em = $this->get('doctrine')->getManager();
        $userCount = $em->getRepository('AppBundle:User')->count();

        return $this->render(
            'admin/user/export.html.twig',
            [
                'userCount' => $userCount,
            ]
        );
    }

    /**
     * Exports user list in csv format.
     * @Route("/export-csv")
     */
    public function exportCSVAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $users = $em->getRepository('AppBundle:User')->export($request->query);
        $response = $this->exportCSV($users);

        return new Response(
            $response, 200, [
                         'Content-Type'        => 'application/force-download',
                         'Content-Disposition' => 'attachment; filename="users.csv"',
                     ]
        );
    }

    /**
     * @Route("/export-json")
     */
    public function exportJSONAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:User')->export($request->query);
        $response = json_encode($posts);

        return new Response(
            $response, 200, [
                         'Content-Type'        => 'application/force-download',
                         'Content-Disposition' => 'attachment; filename="users.json"',
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
                'form' => $form->createView(),
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
        }

        // Get CSV reader service
        $reader = $this->get('services.csv_reader');
        $counter = 0;
        $dupes = 0;
        // File check
        if (is_file($file)) {
            // Init reader service
            $reader->init($file, 0, ';');
            // Load user entity
            $em = $this->get('doctrine')->getManager();
            $users = $em->getRepository('AppBundle:User');
            // Read current line
            while (false !== ($line = $reader->readLine())) {

                // Check import validity
                // Minimum required to import user are username and email
                $username = $line->get('user_username');
                $email = $line->get('user_email');

                if (!$email || !$username) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'Le format du fichier que vous tentez d\'importer n\'est pas valide'
                    );

                    return $this->redirectToRoute('app_admin_user_index');
                }

                // Check if user with same email exists already
                $user = $users->findOneByEmail($line->get('user_email'));
                // When not found persist new user
                if (!$user) {
                    $counter++;
                    $user = new User();
                    $user->setUsername($username)
                        ->setEmail($email);

                    $slug = $line->get('user_slug');
                    if ($slug) {
                        $user->setSlug($slug);
                    }

                    $bio = $line->get('user_bio');
                    if ($bio) {
                        $user->setBio($bio);
                    }

                    $password = $line->get('user_password');
                    if ($password) {
                        $user->setPassword($password);
                    }

                    $avatar = $line->get('user_avatar');
                    if ($avatar) {
                        $user->setAvatar($avatar);
                    }

                    $roles = $line->get('user_roles');
                    if ($roles) {
                        $user->setRoles(explode(',', $line->get('user_roles')));
                    }

                    $created = $line->get('user_created');
                    if ($created) {
                        $user->setCreated(date_create_from_format('Y/m/d H:i:s', $line->get('user_created')));
                    }

                    $modified = $line->get('user_modified');
                    if ($modified) {
                        $user->setModified(date_create_from_format('Y/m/d H:i:s', $line->get('user_modified')));
                    }

                    $em->persist($user);
                    $em->flush();
                } else {
                    $dupes++;
                }
            }
        }

        switch ($counter) {
            case 0:
                $msg = 'Aucun compte utilisateur n\'a été importé.';
                break;
            case 1:
                $msg = 'L\'utilisateur <strong>"'.$user.'"</strong> a bien été importé.';
                break;
            default:
                $msg = $counter.' comptes utilisateur ont été importés.';
        }

        $this->get('session')->getFlashBag()->add('success', $msg);

        return $this->redirectToRoute('app_admin_user_index');
    }
}
