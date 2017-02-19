<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Lists all users.
     *
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
            ->add('Fichier', FileType::class, [
                'constraints' => [
                    new NotBlank(),
                    new File([
//                        'maxSize' => '1024k',
//                        'maxSizeMessage' => "Le fichier que vous tentez d'importer est trop volumineux",
                        'mimeTypes' => 'application/vnd.ms-excel',
                        'mimeTypesMessage' => "Vous ne pouvez importer que des fichiers de type CSV"
                    ]),
                ]
            ])
            ->getForm();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    /**
     * Finds and deletes a User entity.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function deleteAction(Request $request, User $user)
    {
        // Deletes specified user
        $this->get('em')->remove($user);
        $this->get('em')->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add('success', "L'utilisateur ".
            "<strong>&quot;{$user->getUsername()}&quot;</strong> à ".
            "bien été supprimé.");

        // Disconnects user
        if ($user == $this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('homepage');
        }



        // User is redirected to referrer page
        return $this->redirectToRoute('app_admin_index');
    }

    /**
     * Sets user roles.
     *
     * @Route("/add-role/{id}/{role}", requirements={"id": "\d+"})
     */
    public function addRoleAction(Request $request, User $user, $role)
    {
        $user->addRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "Le role <strong>&quot;$role&quot; à été attribué à ".
            "&quot;{$user->getUsername()}&quot;</strong>.");

        // User is redirected to referrer page
        return $this->redirectToRoute('app_admin_index');
    }

    /**
     * Removes user role.
     *
     * @Route("/remove-role/{id}/{role}", requirements={"id": "\d+"}, name="admin_remove_role")
     */
    public function removeRoleAction(Request $request, User $user, $role)
    {
        // Disconnects user who changes his own admin rights
        if ( $user == $this->getUser() ) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('homepage');
        }

        $user->removeRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "Le role <strong>&quot;$role&quot; à été retiré à ".
            "&quot;{$user->getUsername()}&quot;</strong>.");

        // User is redirected to referrer page
        return $this->redirectToRoute('app_admin_index');
    }
}
