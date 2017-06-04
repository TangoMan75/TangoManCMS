<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\AdminEditMediaType;
use AppBundle\Form\Admin\AdminNewMediaType;
use AppBundle\Form\FileUploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 * @Route("/admin/media")
 *
 * @package AppBundle\Controller
 */
class MediaController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated media list
        $em = $this->get('doctrine')->getManager();
        $listMedia = $em->getRepository('AppBundle:Post')->searchableOrderedPaged($request->query);

        return $this->render(
            'admin/media/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'listMedia'   => $listMedia,
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
        $form = $this->createForm(AdminNewMediaType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new media
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Le média <strong>&quot;'.$post.'&quot;</strong> a bien été ajouté.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/media/new.html.twig',
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
        $form = $this->createForm(AdminEditMediaType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited media
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le média <strong>&quot;'.$post.'&quot;</strong> a bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/media/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'media'       => $post,
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish": "\d+"})
     */
    public function publishAction(Request $request, Post $post, $publish)
    {
        $post->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($post);
        $em->flush();

        if ($publish) {
            $message = 'Le média <strong>&quot;'.$post.'&quot;</strong> a bien été publié.';
        } else {
            $message = 'La publication du média <strong>&quot;'.$post.'&quot;</strong> a bien été annulée.';
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
     * Finds and deletes a Media.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Post $post)
    {
        // Deletes specified media
        $em = $this->get('doctrine')->getManager();
        $em->remove($post);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le média <strong>&quot;'.$post.'&quot;</strong> a bien été supprimé.'
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
        $listMedia = $em->getRepository('AppBundle:Post')->findAll();

        return $this->render(
            'admin/media/export.html.twig',
            [
                'currentUser' => $this->getUser(),
                'listMedia'   => $listMedia,
            ]
        );
    }

    /**
     * @Route("/export-json")
     */
    public function exportJSONAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $listMedia = $em->getRepository('AppBundle:Post')->findAllPosts();
        $response = json_encode($listMedia);

        return new Response(
            $response, 200, [
                'Content-Type'        => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="listMedia.json"',
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

                return $this->redirectToRoute('app_admin_media_import');
            }

            return $this->importJSON($file);
        }

        return $this->render(
            'admin/media/import.html.twig',
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
        if ($file->getClientMimeType() !== 'application/json' && !in_array($clientExtension, ['json'])) {
            $this->get('session')->getFlashBag()->add('error', 'Ce format du fichier n\'est pas supporté.');

            return $this->redirectToRoute('app_admin_media_import');
        }

        $counter = 0;
        $dupes = 0;
        // File check
        if (is_file($file)) {
            // Load entities
            $em = $this->get('doctrine')->getManager();
            $listMedia = $em->getRepository('AppBundle:Post');
            $tags = $em->getRepository('AppBundle:Tag');
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

                // Check if media exists already
                if (!$listMedia->findOneBySlug($import->media_slug)) {
                    $counter++;

                    // Check if media author exists
                    $user = $users->findOneByEmail($import->user_email);
                    // When author doesn't exist sets current user as author
                    if (!$user) {
                        $user = $this->getUser();
                    }

                    $post = new Post();
                    $post->setUser($user)
                        ->setTitle($import->media_title)
                        ->setSlug($import->media_slug)
                        ->addTag($tag)
                        ->setText($import->media_text);

                    // $post->setCreated($import->media_created);
                    // $post->setModified($import->media_modified);

                    $em->persist($post);
                    $em->flush();
                }
            }

            if ($counter > 1) {
                $success = $counter.'medias ont été importés.';
            } else {
                $success = 'Aucun media n\'a été importé.';
            }

            $this->get('session')->getFlashBag()->add('success', $success);
        }

        return $this->redirectToRoute('app_admin_media_index');
    }
}
