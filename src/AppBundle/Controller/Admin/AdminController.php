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
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        return $this->render(
            'admin/default/index.html.twig',
            [
                'commentCount'   => $this->get('doctrine')->getRepository('AppBundle:Comment')->countBy(),
                'pageCount'      => $this->get('doctrine')->getRepository('AppBundle:Page')->countBy(),
                'postCount'      => $this->get('doctrine')->getRepository('AppBundle:Post')->countBy(['type' => 'post']),
                'mediaCount'     => $this->get('doctrine')->getRepository('AppBundle:Post')->countBy(
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
                ),
                'galleryCount'   => $this->get('doctrine')->getRepository('AppBundle:Section')->countBy(['type' => 'gallery']),
                'sectionCount'   => $this->get('doctrine')->getRepository('AppBundle:Section')->countBy(['type' => 'section']),
                'userCount'      => $this->get('doctrine')->getRepository('AppBundle:User')->countBy(),
                'siteCount'      => $this->get('doctrine')->getRepository('AppBundle:Site')->countBy(),
            ]
        );
    }
}
