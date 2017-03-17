<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Display User entity.
     * @Route("/{slug}", requirements={"slug": "[\w-]+"})
     *
     * @param Request $request
     * @param         $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $slug)
    {

        $em = $this->get('doctrine')->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['slug' => $slug]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas.');
        }

        // Admin or author only can see user unpublished posts
        if ($this->getUser() === $user || $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $posts = $em->getRepository('AppBundle:Post')->findByUserPaged(
                $user,
                $request->query->getInt('page', 1),
                5,
                false
            );
        } else {
            $posts = $em->getRepository('AppBundle:Post')->findByUserPaged(
                $user,
                $request->query->getInt('page', 1),
                5
            );
        }

        return $this->render(
            'user/show.html.twig',
            [
                'user'  => $user,
                'posts' => $posts,
            ]
        );
    }

    /**
     * Edit User.
     * @Route("/edit/{slug}", requirements={"slug": "[\w-]+"})
     * @ParamConverter("user", class="AppBundle:User")
     *
     * @param Request $request
     * @param         $slug
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['slug' => $slug]);

        if ($this->getUser() !== $user && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à modifier cet utilisateur.');

            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Votre profil a bien été enregistré.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'user'       => $user,
                'formAvatar' => $form->createView(),
            ]
        );
    }
}
