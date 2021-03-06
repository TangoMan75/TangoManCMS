<?php
/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MediaController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/api/admin/medias")
 */
class MediaController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em        = $this->get('doctrine')->getManager();
        $listMedia = $em->getRepository('AppBundle:Post')->findByQueryScalar(
            $request,
            [
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
            ]
        );

        return new JsonResponse(
            ['listMedia' => $listMedia]
        );
    }
}
