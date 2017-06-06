<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Section;
use AppBundle\Form\Admin\AdminNewGalleryType;
use AppBundle\Form\Admin\AdminEditGalleryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GalleryController
 * @Route("/admin/galleries")
 *
 * @package AppBundle\Controller
 */
class GalleryController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated gallery list
        $em = $this->get('doctrine')->getManager();
        $galleries = $em->getRepository('AppBundle:Section')->searchableOrderedPaged($request->query);

        return $this->render(
            'admin/gallery/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'galleries'    => $galleries,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $gallery = new Section();
        $form = $this->createForm(AdminNewGalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new gallery
            $em = $this->get('doctrine')->getManager();
            $em->persist($gallery);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'La galerie <strong>&quot;'.$gallery.'&quot;</strong> a bien été ajoutée.');

            // User is redirected to referrer gallery
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/gallery/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish": "\d+"})
     */
    public function publishAction(Request $request, Gallery $gallery, $publish)
    {
        $gallery->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($gallery);
        $em->flush();

        if ($publish) {
            $message = 'La gallery <strong>&quot;'.$gallery.'&quot;</strong> a bien été publiée.';
        } else {
            $message = 'La publication de la galerie <strong>&quot;'.$gallery.'&quot;</strong> a bien été annulée.';
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
    public function editAction(Request $request, Gallery $gallery)
    {
        $form = $this->createForm(AdminEditGalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited gallery
            $em = $this->get('doctrine')->getManager();
            $em->persist($gallery);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'La galerie <strong>&quot;'.$gallery.'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/gallery/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'gallery'     => $gallery,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Gallery $gallery)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            // User is redirected to referrer gallery
            return $this->redirect($request->get('callback'));
        }

        // Deletes specified gallery
        $em = $this->get('doctrine')->getManager();
        $em->remove($gallery);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La galerie <strong>&quot;'.$gallery.'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
