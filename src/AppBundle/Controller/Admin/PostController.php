<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\AdminEditPostType;
use AppBundle\Form\Admin\AdminNewPostType;
use AppBundle\Form\FileUploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TangoMan\CSVExportHelper\CSVExportHelper;

/**
 * Class PostController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/admin/posts")
 */
class PostController extends Controller
{

    use CSVExportHelper;

    /**
     * @Route("/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated post list
        $em    = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findByQuery(
            $request,
            ['type' => 'post']
        );

        return $this->render(
            'admin/post/index.html.twig',
            [
                'posts' => $posts,
            ]
        );
    }

    /**
     * @Route("/new")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setUser($this->getUser());
        $form = $this->createForm(AdminNewPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new post
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'article <strong>&quot;'.$post
                .'&quot;</strong> a bien été ajouté.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/post/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish":
     *                                   "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     * @param                                           $publish
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction(Request $request, Post $post, $publish)
    {
        $post->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($post);
        $em->flush();

        if ($publish) {
            $message = 'L\'article <strong>&quot;'.$post
                       .'&quot;</strong> a bien été publié.';
        } else {
            $message = 'La publication de l\'article <strong>&quot;'.$post
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Post $post)
    {
        $form = $this->createForm(AdminEditPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited post
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'article <strong>&quot;'.$post
                .'&quot;</strong> a bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/post/edit.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }

    /**
     * Finds and deletes a Post.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
            'L\'article <strong>&quot;'.$post
            .'&quot;</strong> a bien été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/export")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportAction(Request $request)
    {
        $em        = $this->get('doctrine')->getManager();
        $postCount = $em->getRepository('AppBundle:Post')->countBy();

        return $this->render(
            'admin/post/export.html.twig',
            [
                'postCount' => $postCount,
            ]
        );
    }

    /**
     * Exports post list in csv format.
     * @Route("/export-csv")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportCSVAction(Request $request)
    {
        $em       = $this->get('doctrine')->getManager();
        $posts    = $em->getRepository('AppBundle:Post')->export($request);
        $response = $this->exportCSV($posts);

        return new Response(
            $response, 200, [
                'Content-Type'        => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="posts.csv"',
            ]
        );
    }

    /**
     * @Route("/export-json")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportJSONAction(Request $request)
    {
        $em       = $this->get('doctrine')->getManager();
        $posts    = $em->getRepository('AppBundle:Post')->export($request);
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $file = $request->files->get('file_upload')['file'];

            if ( ! $file->isValid()) {
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
                'form' => $form->createView(),
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
        if ($file->getClientMimeType() !== 'application/json'
            && ! in_array(
                $clientExtension,
                ['json']
            )) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Ce format du fichier n\'est pas supporté.'
            );

            return $this->redirectToRoute('app_admin_post_import');
        }

        $counter = 0;
        $dupes   = 0;
        // File check
        if (is_file($file)) {
            // Load entities
            $em    = $this->get('doctrine')->getManager();
            $posts = $em->getRepository('AppBundle:Post');
            $tags  = $em->getRepository('AppBundle:Tag');
            $users = $em->getRepository('AppBundle:User');
            $tag   = $tags->findOneByName('import');

            foreach (json_decode(file_get_contents($file)) as $import) {

                // Check if post exists already
                if ( ! $posts->findOneBySlug($import->post_slug)) {
                    $counter++;

                    // Check if post author exists
                    $user = $users->findOneByEmail($import->user_email);
                    // When author doesn't exist sets current user as author
                    if ( ! $user) {
                        $user = $this->getUser();
                    }

                    $post = new Post();
                    $post->setUser($user)
                         ->setTitle($import->post_title)
                         ->setSlug($import->post_slug)
                         ->addTag($tag)
                         ->setText($import->post_text);

                    // $post->setCreated($import->post_created);
                    // $post->setModified($import->post_modified);

                    $em->persist($post);
                    $em->flush();
                }
            }

            if ($counter > 1) {
                $success = $counter.'articles ont été importés.';
            } else {
                $success = 'Aucun article n\'a été importé.';
            }

            $this->get('session')->getFlashBag()->add('success', $success);
        }

        return $this->redirectToRoute('app_admin_post_index');
    }
}
