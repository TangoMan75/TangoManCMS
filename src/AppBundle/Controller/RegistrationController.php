<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="app_register")
     */
    public function registerAction(Request $request)
    {
        $user = new User;

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success','Votre inscription a bien été prise en compte.');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'form_register' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit", name="app_edit")
     */
    public function editAction(Request $request)
    {
    }

    /**
     * @Route("/confirm", name="app_confirm")
     */
    public function confirmAction(Request $request)
    {
    }

    /**
     * @Route("/delete", name="app_delete")
     */
    public function deleteAction(Request $request)
    {
    }

}
