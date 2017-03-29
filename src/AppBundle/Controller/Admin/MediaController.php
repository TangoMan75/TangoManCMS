<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Media;
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
        $listMedia = $em->getRepository('AppBundle:Media')->orderedSearchPaged($request->query);

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
        $media = new Media();
        $media->setUser($this->getUser());
        $form = $this->createForm(AdminNewMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new media
            $em = $this->get('doctrine')->getManager();
            $em->persist($media);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Le média <strong>&quot;'.$media.'&quot;</strong> a bien été ajouté.'
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
    public function editAction(Request $request, Media $media)
    {
        $form = $this->createForm(AdminEditMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited media
            $em = $this->get('doctrine')->getManager();
            $em->persist($media);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le média <strong>&quot;'.$media.'&quot;</strong> a bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/media/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'media'       => $media,
            ]
        );
    }

    /**
     * Finds and deletes a Media.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Media $media)
    {
        // Deletes specified media
        $em = $this->get('doctrine')->getManager();
        $em->remove($media);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le média <strong>&quot;'.$media.'&quot;</strong> a bien été supprimé.'
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
        $listMedia = $em->getRepository('AppBundle:Media')->findAll();

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
        $listMedia = $em->getRepository('AppBundle:Media')->findAllMedias();
        $response = json_encode($listMedia);

        return new Response(
            $response, 200, [
                'Content-Type' => 'application/force-download',
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
            $listMedia = $em->getRepository('AppBundle:Media');
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

                    $media = new Media();
                    $media->setUser($user)
                        ->setTitle($import->media_title)
                        ->setSlug($import->media_slug)
                        ->addTag($tag)
                        ->setDescription($import->media_content);

                    // $media->setCreated($import->media_created);
                    // $media->setModified($import->media_modified);

                    $em->persist($media);
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

        return $this->redirectToRoute('app_admin_media_index');
    }
}
