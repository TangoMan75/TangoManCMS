<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\AdminTagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagController
 * @Route("/admin/tags")
 *
 * @package AppBundle\Controller
 */
class TagController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated page list
        $em = $this->get('doctrine')->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->sortedSearchPaged($request->query);

        return $this->render(
            'admin/tag/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'tags'        => $tags,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(AdminTagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'L\'étiquette <strong>&quot;'.$tag.'&quot;</strong> a bien été ajoutée.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/tag/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Tag $tag)
    {
        $form = $this->createForm(AdminTagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($tag);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'étiquette <strong>&quot;'.$tag.'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/tag/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'tag'         => $tag,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        // Deletes specified tag
        $em = $this->get('doctrine')->getManager();
        $em->remove($tag);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'étiquette <strong>&quot;'.$tag.'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
