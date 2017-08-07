<?php

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/admin/medias")
 */
class MediaController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $listMedia = $em->getRepository('AppBundle:Post')->findByQueryScalar(            $request->query, [
                               'type' => [
                                   '360',
                                   'argus360',
                                   'csv',
                                   'dailymotion',
                                   'doc',
                                   'document',
                                   'embed',
                                   'gif',
                                   'giphy',
                                   'gist',
                                   'image',
                                   'jpeg',
                                   'jpg',
                                   'ods',
                                   'odt',
                                   'pdf',
                                   'png',
                                   'pptx',
                                   'thetas',
                                   'tweet',
                                   'txt',
                                   'vimeo',
                                   'xls',
                                   'youtube',
                               ],
                           ]);

        return new JsonResponse(
            ['listMedia' => $listMedia]
        );
    }
}
