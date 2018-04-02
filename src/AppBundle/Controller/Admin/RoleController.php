<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
 *core/pdo.php
 */
class RoleController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated role list
        $em    = $this->get('doctrine')->getManager();
        $roles = $em->getRepository('AppBundle:Role')->findByQuery($request);

        return $this->render(
            'admin/role/index.html.twig',
            [
                'roles' => $roles,
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
                'Le role <strong>&quot;'.$role
                .'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/role/new.html.twig',
            [
                'form' => $form->createView(),
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
                'Le role <strong>&quot;'.$role
                .'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/role/edit.html.twig',
            [
                'form' => $form->createView(),
                'role' => $role,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Role $role)
    {
        // Only author or admin can edit comment
        if (in_array(
            $role->getType(),
            [
                'ROLE_USER',
                'ROLE_SUPER_USER',
                'ROLE_ADMIN',
                'ROLE_SUPER_ADMIN',
            ]
        )) {
            $this->get('session')->getFlashBag()->add('error',
                'Il n\'est pas possible de supprimer le role <strong>&quot;'
                .$role->getName().'&quot;</strong>.'
            );

            return $this->redirect($request->get('callback'));
        }

        // Deletes specified role
        $em = $this->get('doctrine')->getManager();
        $em->remove($role);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le role <strong>&quot;'.$role->getName()
            .'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
