<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PwdType;
use TangoMan\JWTBundle\Model\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/jwt")
 */
class JWTController extends Controller
{
    /**
     * setTokenAction
     *
     * @param string $token
     * @Route("/encode")
     */
    public function setTokenAction()
    {
        $jwtService = $this->get('jwt');

        $jwt = new JWT();
        $jwt->set('email', 'admin@example.com');
        $jwt->set('username', 'Admin');
        $jwt->setPeriod(new \DateTime(), new \DateTime('+3 days'));
        $token = $jwtService->encode($jwt);

        $jwt2 = new JWT();
        $jwt2->set('email', 'admin@example.com');
        $jwt2->set('username', 'Admin');
        $jwt2->setPeriod(new \DateTime('+1 day'), new \DateTime('+3 days'));
        $token2 = $jwtService->encode($jwt2);

        $jwt3 = new JWT();
        $jwt3->set('email', 'admin@example.com');
        $jwt3->set('username', 'Admin');
        $jwt3->setPeriod(new \DateTime('-3 days'), new \DateTime('-1 days'));
        $token3 = $jwtService->encode($jwt3);

        dump($jwt);
        dump($token);
        dump($jwt2);
        dump($token2);
        dump($jwt3);
        dump($token3);
        die();

        return new JsonResponse( array($token, $token2, $token3) );
    }

    /**
     * getTokenAction
     *
     * @Route("/decode/{token}")
     */
    public function getTokenAction(Request $request, $token)
    {
        $jwt = $this->get('jwt')->decode($token);

//        dump($jwt);
//        dump($token);
//        die();

        if (!$jwt->isSignatureValid()){
            throw $this->createNotFoundException("La signature du token n'est pas valide.");
        }

        if ($jwt->isTooSoon()){
            throw $this->createNotFoundException("Le token n'est pas encore valide.");
        }

        if ($jwt->isTooLate()){
            throw $this->createNotFoundException("Le token est expiré.");
        }

        return new JsonResponse( array('success' => "Ce token est valide.") );
    }
}
