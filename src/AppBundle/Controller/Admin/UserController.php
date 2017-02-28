<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        // Show paginated user list
        $users = $this->get('em')->repository('AppBundle:User')->sorting(
            $request->query->getInt('page', 1),
            20,
            $request->query->get('order', 'username'),
            $request->query->get('way', 'ASC')
        );

        $form = $this->createFormBuilder(['csrf_protection' => false])
            ->setAction('import')
            ->add(
                'Fichier',
                FileType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new File(
                            [
//                        'maxSize' => '1024k',
//                        'maxSizeMessage' => "Le fichier que vous tentez d'importer est trop volumineux",
                                'mimeTypes'        => 'application/vnd.ms-excel',
                                'mimeTypesMessage' => 'Vous ne pouvez importer que des fichiers de type CSV',
                            ]
                        ),
                    ],
                ]
            )
            ->getForm();

        return $this->render(
            'admin/user/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'users'       => $users,
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction()
    {
        return $this->render(
            'admin/user/new.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/edit")
     */
    public function editAction()
    {
        return $this->render(
            'admin/user/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
            ]
        );
    }

    /**
     * @Route("/import-export")
     * @Method("POST")
     */
    public function importExportAction(Request $request)
    {
        $form = $this->createFormBuilder(['csrf_protection' => false])
            ->setAction('import')
            ->add(
                'Fichier',
                FileType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new File(
                            [
//                        'maxSize' => '1024k',
//                        'maxSizeMessage' => "Le fichier que vous tentez d'importer est trop volumineux",
                                'mimeTypes'        => 'application/vnd.ms-excel',
                                'mimeTypesMessage' => 'Vous ne pouvez importer que des fichiers de type CSV',
                            ]
                        ),
                    ],
                ]
            )
            ->getForm();

        return $this->render(
            'admin/user/import-export.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * Finds and deletes a User entity.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function deleteAction(Request $request, User $user)
    {
        $admin = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$admin->getUsername().'</strong><br />'.
                'Il n\'est pas autorisé de supprimer un utilisateur ayant des droit d\'administration.'
            );

            return $this->redirectToRoute('app_admin_user_index');
        }

        // Deletes specified user
        $this->get('em')->remove($user);
        $this->get('em')->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'utilisateur '.
            '<strong>&quot;'.$user->getUsername().'&quot;</strong> à '.
            'bien été supprimé.'
        );

        // Admin is redirected to referrer page
        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * Sets user roles.
     * @Route("/add-role/{id}/{role}", requirements={"id": "\d+"})
     */
    public function addRoleAction(Request $request, User $user, $role)
    {
        $user->addRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$role.'&quot; à été attribué à '.
            '&quot;'.$user->getUsername().'&quot;</strong>.'
        );

        // User is redirected to referrer page
        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * Removes user role.
     * @Route("/remove-role/{id}/{role}", requirements={"id": "\d+"}, name="admin_remove_role")
     */
    public function removeRoleAction(Request $request, User $user, $role)
    {
        // Admin can't change his own rigths
        if ($user == $this->getUser() && $role == 'ROLE_ADMIN') {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$user->getUsername().'</strong><br />'.
                'Vous n\'êtes pas autorisé à supprimer vos propres droit d\'administration.'
            );

            return $this->redirectToRoute('app_admin_user_index');
        }

        $user->removeRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$role.'&quot; à été retiré à '.
            '&quot;'.$user->getUsername().'&quot;</strong>.'
        );

        // User is redirected to referrer page
        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * @Route("/import-csv")
     * @Method("POST")
     */
    public function importCSVAction(Request $request)
    {
        $form = $this->createFormBuilder(['csrf_protection' => false])
//            ->setAction('import')
            ->add(
                'Fichier',
                FileType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new File(
                            [
//                        'maxSize' => '1024k',
//                        'maxSizeMessage' => "Le fichier que vous tentez d'importer est trop volumineux",
                                'mimeTypes'        => 'application/vnd.ms-excel',
                                'mimeTypesMessage' => "Vous ne pouvez importer que des fichiers de type CSV",
                            ]
                        ),
                    ],
                ]
            )
            ->getForm();

        $form->handleRequest($request);
        $file = $request->files->get('form')['Fichier'];

        // Upload success check
        if (!$file->isValid()) {
            $this->get('session')->getFlashBag()->add(
                'error',
                "Une erreur s'est produite lors du transfert. <br />Veuillez réessayer."
            );

            return $this->redirectToRoute('app_admin_user_index');
        }

        // Security checks
        $validExtensions = ['csv', 'tsv'];
        $clientExtension = $file->getClientOriginalExtension();

        if ($file->getClientMimeType() !== 'application/vnd.ms-excel' &&
            !in_array($clientExtension, $validExtensions)
        ) {

            $this->get('session')->getFlashBag()->add('error', "Ce format du fichier n'est pas supporté.");

            return $this->redirectToRoute('app_admin_user_index');
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
                $em = $this->get('em');
                $users = $em->repository('AppBundle:User');
                while (false !== ($line = $reader->readLine())) {
                    // Check if user with same email already exist
                    $user = $users->findOneByEmail($line->get('email'));
                    // When not found persist new user
                    if (!$user) {
                        $counter++;
                        $user = new User();
                        $user->setUsername($line->get('username'));
                        $user->setEmail($line->get('email'));
                        $user->setPassword($line->get('password'));
                        $user->setAvatar($line->get('avatar'));
                        $user->setRoles(explode(",", $line->get('roles')));
                        $user->setDateCreated(date_create_from_format('Y/m/d H:i:s', $line->get('date_created')));
                        $em->save($user);
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
     * @Method("GET")
     */
    public function exportCSVAction()
    {
        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);
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
