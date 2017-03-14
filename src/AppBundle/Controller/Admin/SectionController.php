<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Section;
use AppBundle\Form\AdminSectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SectionController
 * @Route("/admin/sections")
 *
 * @package AppBundle\Controller
 */
class SectionController extends Controller
{
    /**
     * Lists all sections.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated section list
        $em = $this->get('doctrine')->getManager();
        $sections = $em->getRepository('AppBundle:Section')->sortedSearchPaged($request->query);

        return $this->render(
            'admin/section/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'sections'    => $sections,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $section = new Section();
        $form = $this->createForm(AdminSectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new section
            $em = $this->get('doctrine')->getManager();
            $em->persist($section);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'La section a bien été ajoutée.');

            // User is redirected to referrer section
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/section/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Section $section)
    {
        $form = $this->createForm(AdminSectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited section
            $em = $this->get('doctrine')->getManager();
            $em->persist($section);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'La section <strong>&quot;'.$section.'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer section
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/section/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'section'     => $section,
            ]
        );
    }

    /**
     * Finds and deletes a Section.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Section $section)
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$user.'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            return $this->redirectToRoute('app_admin_section_index');
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($section);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La section <strong>&quot;'.$section.'&quot;</strong> a bien été supprimée.'
        );

        // Admin is redirected to referrer section
        return $this->redirectToRoute('app_admin_page_index');
    }

}
