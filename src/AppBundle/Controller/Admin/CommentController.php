<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Form\AdminEditCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @Route("/admin/comments")
 *
 * @package AppBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * Lists all comments.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated page list
        $em = $this->get('doctrine')->getManager();
        $comments = $em->getRepository('AppBundle:Comment')->sortedSearchPaged($request->query);

        return $this->render(
            'admin/comment/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'comments'       => $comments,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $page = new Comment();
        $form = $this->createForm(AdminEditCommentType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'La page a bien été ajoutée.');

            // User is redirected to referrer page
            return $this->redirectToRoute('app_admin_page_index');
        }

        return $this->render(
            'admin/page/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Comment $page)
    {
        $form = $this->createForm(AdminEditCommentType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add('success', 'La page a bien été modifiée.');

            return $this->redirectToRoute('app_admin_page_index');
        }

        return $this->render(
            'admin/page/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'page'        => $page,
            ]
        );
    }

    /**
     * Finds and deletes a Comment.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Comment $page)
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$user->getUsername().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            return $this->redirectToRoute('app_admin_page_index');
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($page);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La page <strong>&quot;'.$page->getTitle().'&quot;</strong> à bien été supprimée.'
        );

        // Admin is redirected to referrer page
        return $this->redirectToRoute('app_admin_page_index');
    }

}
