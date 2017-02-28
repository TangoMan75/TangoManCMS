<?php

namespace AppBundle\Controller\Admin;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Lists all users.
     *
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        return $this->render('admin/index.html.twig', [
            'currentUser' => $this->getUser(),
        ]);
    }
}
