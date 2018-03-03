<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Section;
use AppBundle\Form\Admin\AdminNewSectionType;
use AppBundle\Form\Admin\AdminEditSectionType;
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
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated section list
        $em       = $this->get('doctrine')->getManager();
        $sections = $em->getRepository('AppBundle:Section')->findByQuery(
            $request,
            ['type' => 'section']
        );

        return $this->render(
            'admin/section/index.html.twig',
            [
                'sections' => $sections,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $section = new Section();
        $form    = $this->createForm(AdminNewSectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new section
            $em = $this->get('doctrine')->getManager();
            $em->persist($section);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'La section <strong>&quot;'.$section
                .'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer section
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/section/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish":
     *                                   "\d+"})
     */
    public function publishAction(Request $request, Section $section, $publish)
    {
        $section->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($section);
        $em->flush();

        if ($publish) {
            $message = 'La section <strong>&quot;'.$section
                       .'&quot;</strong> a bien été publiée.';
        } else {
            $message = 'La publication de la section <strong>&quot;'.$section
                       .'&quot;</strong> a bien été annulée.';
        }

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            $message
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Section $section)
    {
        $form = $this->createForm(AdminEditSectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited section
            $em = $this->get('doctrine')->getManager();
            $em->persist($section);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'La section <strong>&quot;'.$section
                .'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/section/edit.html.twig',
            [
                'form'    => $form->createView(),
                'section' => $section,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Section $section)
    {
        if ( ! $this->get('security.authorization_checker')->isGranted(
            'ROLE_SUPER_ADMIN'
        )) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            // User is redirected to referrer section
            return $this->redirect($request->get('callback'));
        }

        // Deletes specified section
        $em = $this->get('doctrine')->getManager();
        $em->remove($section);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La section <strong>&quot;'.$section
            .'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
