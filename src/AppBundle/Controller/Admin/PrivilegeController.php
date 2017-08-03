<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Privilege;
use AppBundle\Form\Admin\AdminEditPrivilegeType;
use AppBundle\Form\Admin\AdminNewPrivilegeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PrivilegeController
 * @Route("/admin/privileges")
 *
 * @package AppBundle\Controller
 */
class PrivilegeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated privilege list
        $em = $this->get('doctrine')->getManager();
        $privileges = $em->getRepository('AppBundle:Privilege')->findByQuery($request->query);

        return $this->render(
            'admin/privilege/index.html.twig',
            [
                'privileges' => $privileges,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $privilege = new Privilege();
        $form = $this->createForm(AdminNewPrivilegeType::class, $privilege);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new privilege
            $em = $this->get('doctrine')->getManager();
            $em->persist($privilege);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Le privilege <strong>&quot;'.$privilege.'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/privilege/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Privilege $privilege)
    {
        $form = $this->createForm(AdminEditPrivilegeType::class, $privilege);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited privilege
            $em = $this->get('doctrine')->getManager();
            $em->persist($privilege);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le privilege <strong>&quot;'.$privilege.'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/privilege/edit.html.twig',
            [
                'form'      => $form->createView(),
                'privilege' => $privilege,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Privilege $privilege)
    {
        // Only author or admin can edit comment
        if (in_array(
            $privilege->getType(), [
                                     'ROLE_USER',
                                     'ROLE_SUPER_USER',
                                     'ROLE_ADMIN',
                                     'ROLE_SUPER_ADMIN',
                                 ]
        )) {
            $this->get('session')->getFlashBag()->add('error', 'Il n\'est pas possible de supprimer le privilege <strong>&quot;'.$privilege->getName().'&quot;</strong>.');

            return $this->redirect($request->get('callback'));
        }

        // Deletes specified privilege
        $em = $this->get('doctrine')->getManager();
        $em->remove($privilege);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le privilege <strong>&quot;'.$privilege->getName().'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
