<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\CommentType;
use AppBundle\Form\EditMediaType;
use AppBundle\Form\NewMediaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

        $medias = $em->getRepository('AppBundle:Post')->findByTagPaged($tag, $request->query->getInt('page', 1), 5);
        $formView = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
            $media = new Post();
            $media->setUser($user);

            $form = $this->createForm(NewMediaType::class, $media);
            $form->handleRequest($request);
            $formView = $form->createView();

            if ($form->isValid()) {
                $em->persist($media);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Votre publication a bien été enregistré.');

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'form'   => $formView,
                'medias' => $medias,
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
        $media = $em->getRepository('AppBundle:Post')->findOneBy(['slug' => $slug]);

        return $this->render(
            'media/show.html.twig',
            [
                'media' => $media,
            ]
        );
    }

    /**
     * @Route("/new")
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request)
    {
        $media = new Post();
        $media->setUser($this->getUser());
        $form = $this->createForm(NewMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($media);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'La publication intitulé <strong>'.$media.'</strong> a bien été enregistré.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'media/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, Post $media)
    {
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
                'Votre publication <strong>&quot;'.$media.'&quot</strong> à bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'media/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, Post $media)
    {
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
            'La publication <strong>&quot;'.$media.'&quot;</strong> à été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
