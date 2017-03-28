<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Page;
use AppBundle\Form\Admin\AdminNewPageType;
use AppBundle\Form\Admin\AdminEditPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageController
 * @Route("/admin/pages")
 *
 * @package AppBundle\Controller
 */
class PageController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated page list
        $em = $this->get('doctrine')->getManager();
        $pages = $em->getRepository('AppBundle:Page')->orderedSearchPaged($request->query);

        return $this->render(
            'admin/page/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'pages'       => $pages,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm(AdminNewPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'La page <strong>&quot;'.$page.'&quot;</strong> a bien été ajoutée.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
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
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createForm(AdminEditPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'La page <strong>&quot;'.$page.'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
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
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Page $page)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($page);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La page <strong>&quot;'.$page.'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
