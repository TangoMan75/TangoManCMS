<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="app_homepage")
	 */
	public function indexAction(Request $request)
	{
		$listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findByPage($request->query->getInt('page', 1), 5);

		$formPost = null;

		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

			$user = $this->getUser();

			$post = new Post();
			$post->setUser($user);

			$form = $this->createForm(PostType::class, $post);
			$form->handleRequest($request);
			$formPost = $form->createView();

			if ($form->isValid()) {
				$manager = $this->getDoctrine()->getManager();
				$manager->persist($post);
				$manager->flush();
				$this->get('session')->getFlashBag()->add('success', 'Votre message a bien été enregistré.');
				return $this->redirectToRoute('app_homepage');
			}
		}

		return $this->render('default/index.html.twig', [
			'form_post' => $formPost,
			'list_post' => $listPost,
		]);
	}

}
