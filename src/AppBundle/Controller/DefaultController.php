<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PostType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function indexAction(Request $request)
    {
        // $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->find(1);
        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findAll();
        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['author' => 'Fabrice']);
        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->idSuperior(1);
        // $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['id' => $id]);

        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy([], ['datetime' => 'desc']);

//        $now = date(time());
//
//        $listPost = [
//            'Post' => [
//                'id' => 1,
//                'author' => 'livredor.dev',
//                'title' => 'Aucun messages',
//                'datetime' => $now,
//                'content' => "Personne n'a publié de messages sur le livre d'or.",
//            ],
//        ];

//        if (!$listPost) {};

        $post = new Post();

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $post->setAuthor($this->getUser()->getUserName());
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success','Votre message a bien été enregistré');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('default/index.html.twig', [
            'form_post' => $form->createView(),
            'list_post' => $listPost
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function loginAction(){
        $helper = $this->get('security.authentication_utils');

        return $this->render('default/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logoutAction(){
    }

    /**
     * @Route("/login-check", name="app_login_check")
     */
    public function loginCheckAction(){
    }
}
