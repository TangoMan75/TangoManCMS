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
                'commentCount'   => $this->get('doctrine')->getRepository('AppBundle:Comment')->countByCriteria(),
                'pageCount'      => $this->get('doctrine')->getRepository('AppBundle:Page')->countByCriteria(),
                'postCount'      => $this->get('doctrine')->getRepository('AppBundle:Post')->countByCriteria(['type' => 'post']),
                'mediaCount'     => $this->get('doctrine')->getRepository('AppBundle:Post')->countByCriteria(
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
                'galleryCount'   => $this->get('doctrine')->getRepository('AppBundle:Section')->countByCriteria(['type' => 'gallery']),
                'sectionCount'   => $this->get('doctrine')->getRepository('AppBundle:Section')->countByCriteria(['type' => 'section']),
                'userCount'      => $this->get('doctrine')->getRepository('AppBundle:User')->countByCriteria(),
                'siteCount'      => $this->get('doctrine')->getRepository('AppBundle:Site')->countByCriteria(),
            ]
        );
    }
}
