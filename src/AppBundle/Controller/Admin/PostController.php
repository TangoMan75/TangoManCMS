<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\EditPostType;
use AppBundle\Form\FileUploadType;
use AppBundle\Form\NewPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        // Show searchable, sortable, paginated post list
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:Post')->sortedSearchPaged($request->query);

        return $this->render(
            'admin/post/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'posts'       => $posts,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setUser($this->getUser());
        $form = $this->createForm(NewPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new post
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'L\'article a bien été ajouté.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
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
        $form = $this->createForm(EditPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited post
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add('success', 'L\'article a bien été modifié.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
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
        // Deletes specified post
        $em = $this->get('doctrine')->getManager();
        $em->remove($post);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'article <strong>&quot;'.$post->getTitle().'&quot;</strong> a bien été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/export")
     */
    public function exportAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAll();

        return $this->render(
            'admin/post/export.html.twig',
            [
                'currentUser' => $this->getUser(),
                'posts'       => $posts,
            ]
        );
    }

    /**
     * @Route("/export-json")
     */
    public function exportJSONAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAllPosts();
        $response = json_encode($posts);

        return new Response(
            $response, 200, [
                'Content-Type'        => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="posts.json"',
            ]
        );
    }

    /**
     * @Route("/import")
     */
    public function importAction(Request $request)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $file = $request->files->get('file_upload')['file'];

            if (!$file->isValid()) {
                // Upload success check
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Une erreur s\'est produite lors du transfert.<br/>Veuillez réessayer.'
                );

                return $this->redirectToRoute('app_admin_post_import');
            }

            return $this->importJSON($file);
        }

        return $this->render(
            'admin/post/import.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @param $file
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importJSON($file)
    {
        // Security checks
        $clientExtension = $file->getClientOriginalExtension();
        if ($file->getClientMimeType() !== 'application/json' && !in_array($clientExtension, ['json']) ) {
            $this->get('session')->getFlashBag()->add('error', 'Ce format du fichier n\'est pas supporté.');
            return $this->redirectToRoute('app_admin_post_import');
        }

        $counter = 0;
        $dupes   = 0;
        // File check
        if (is_file($file)) {
            // Load entities
            $em = $this->get('doctrine')->getManager();
            $posts = $em->getRepository('AppBundle:Post');
            $tags  = $em->getRepository('AppBundle:Tag');
            $users = $em->getRepository('AppBundle:User');

            // Creates "import" tag when non-existent
            $tag = $tags->findOneByName('import');
            if (!$tag) {
                $tag = new Tag();
                $tag->setName('import');
                $tag->setType('default');
                $em->persist($tag);
                $em->flush();
            }

            foreach (json_decode(file_get_contents($file)) as $import) {

                // Check if post exists already
                if (!$posts->findOneBySlug($import->post_slug)) {
                    $counter++;

                    // Check if post author exists
                    $user = $users->findOneByEmail($import->user_email);
                    // When author doesn't exist sets current user as author
                    if (!$user) {
                        $user = $this->getUser();
                    }

                    $post = new Post();
                    $post->setUser($user)
                         ->setTitle($import->post_title)
                         ->setSlug($import->post_slug)
                         ->addTag($tag)
                         ->setContent($import->post_content);

                    // $post->setCreated($import->post_created);
                    // $post->setModified($import->post_modified);

                    $em->persist($post);
                    $em->flush();
                }
            }

            if ($counter > 1) {
                $success = "$counter aticles ont été importés.";
            } else {
                $success = "Aucun article n'a été importé.";
            }

            $this->get('session')->getFlashBag()->add('success', $success);
        }

        return $this->redirectToRoute('app_admin_post_index');
    }
}
