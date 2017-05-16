<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Role;
use AppBundle\Form\Admin\AdminEditRoleType;
use AppBundle\Form\Admin\AdminNewRoleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RoleController
 * @Route("/admin/roles")
 *
 * @package AppBundle\Controller
 */
class RoleController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated role list
        $em = $this->get('doctrine')->getManager();
        $roles = $em->getRepository('AppBundle:Role')->searchableOrderedPage($request->query);

        return $this->render(
            'admin/role/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'roles'       => $roles,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $role = new Role();
        $form = $this->createForm(AdminNewRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new role
            $em = $this->get('doctrine')->getManager();
            $em->persist($role);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Le role <strong>&quot;'.$role.'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/role/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Role $role)
    {
        $form = $this->createForm(AdminEditRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited role
            $em = $this->get('doctrine')->getManager();
            $em->persist($role);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le role <strong>&quot;'.$role.'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/role/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'role'        => $role,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Role $role)
    {
        // Deletes specified role
        $em = $this->get('doctrine')->getManager();
        $em->remove($role);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$role.'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
