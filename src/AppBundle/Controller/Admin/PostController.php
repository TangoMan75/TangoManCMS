<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Form\AdminPostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PostController
 * @Route("/admin/posts")
 *
 * @package AppBundle\Controller
 */
class PostController extends Controller
{
    /**
     * Lists all users.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated user list
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:Post')->sortedSearchPaged($request->query, 10);

        return $this->render(
            'admin/post/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'posts'       => $posts,
                'page'        => $request->query->get('page', 1),
                'order'       => $request->query->get('order', 'title'),
                'way'         => $request->query->get('way', 'ASC'),
                's_id'        => $request->query->get('s_id'),
                's_title'     => $request->query->get('s_title'),
                's_subtitle'  => $request->query->get('s_subtitle'),
                's_content'   => $request->query->get('s_content'),
                's_user'      => $request->query->get('s_user'),
                's_tag'       => $request->query->get('s_tag'),
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(AdminPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new post
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'L\'article a bien été ajouté.');

            // User is redirected to referrer post
            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render(
            'admin/post/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Post $post)
    {
        $form = $this->createForm(AdminPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited post
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add('success', 'L\'article a bien été modifié.');

            return $this->redirectToRoute('app_admin_post_index');
        }

        return $this->render(
            'admin/post/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'post'        => $post,
            ]
        );
    }

    /**
     * Finds and deletes a Post.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Post $post)
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$user->getUsername().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            return $this->redirectToRoute('app_admin_post_index');
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($post);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'article <strong>&quot;'.$post->getTitle().'&quot;</strong> à bien été supprimé.'
        );

        // Admin is redirected to referrer post
        return $this->redirectToRoute('app_admin_post_index');
    }

}
