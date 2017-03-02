<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Page;
use AppBundle\Form\AdminPageType;
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
     * Lists all pages.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show paginated sortable user list
        $pages = $this->get('em')->repository('AppBundle:Page')->findAll();

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
        $form = $this->createForm(AdminPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $this->get('em')->save($page);

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
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createForm(AdminPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $this->get('em')->save($page);
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
     * Finds and deletes a Page.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Page $page)
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
        $this->get('em')->remove($page);
        $this->get('em')->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La page <strong>&quot;'.$page->getTitle().'&quot;</strong> à bien été supprimée.'
        );

        // Admin is redirected to referrer page
        return $this->redirectToRoute('app_admin_page_index');
    }

}
