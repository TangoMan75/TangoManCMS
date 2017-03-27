<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Media;
use AppBundle\Entity\Tag;
use AppBundle\Form\CommentType;
use AppBundle\Form\EditMediaType;
use AppBundle\Form\NewMediaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * Displays media by tag.
     * @Route("/index/{tag}", requirements={"tag": "[\w]+"})
     */
    public function indexAction(Request $request, $tag)
    {
        $em = $this->get('doctrine')->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->findOneByName(['name' => $tag]);

        $medias = $em->getRepository('AppBundle:Media')->findByTagPaged($tag, $request->query->getInt('page', 1), 5);
        $formMedia = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
            $media = new Media();
            $media->setUser($user);
            $form = $this->createForm(NewMediaType::class, $media);
            $form->handleRequest($request);
            $formMedia = $form->createView();

            if ($form->isValid()) {
                $em->persist($media);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Votre message a bien été enregistré.');

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'formMedia' => $formMedia,
                'medias'    => $medias,
            ]
        );
    }

    /**
     * Display media with comments.
     * Allow to publish comments.
     * @Route("/show/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $media = $em->getRepository('AppBundle:Media')->findOneBy(['slug' => $slug]);

        return $this->render(
            'media/show.html.twig',
            [
                'media' => $media,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');

            return $this->redirectToRoute('app_login');
        }

        $media = new Media();
        $media->setUser($this->getUser());
        $form = $this->createForm(NewMediaType::class, $media);
        $form->handleRequest($request);
        $formMedia = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($media);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le message intitulé <strong>'.$media->getTitle().'</strong> a bien été enregistré.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'media/edit.html.twig',
            [
                'formMedia' => $formMedia,
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Media $media)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');

            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit media
        if ($this->getUser() !== $media->getUser() && !$this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');

            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(EditMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->get('doctrine')->getManager();
            $em->persist($media);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Votre message <strong>&quot;'.$media->getTitle().'&quot</strong> à bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'media/edit.html.twig',
            [
                'formMedia' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Media $media)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');

            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit media
        if ($this->getUser() !== $media->getUser() && !$this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');

            return $this->redirectToRoute('homepage');
        }

        // Deletes specified media
        $em = $this->get('doctrine')->getManager();
        $em->remove($media);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le message <strong>&quot;'.$media->getTitle().'&quot;</strong> à été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
