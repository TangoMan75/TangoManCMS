<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Tests\StringableObject;

/**
 * Class CategoriesService
 *
 * @package AppBundle\Service
 */
class CategoriesService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string $type
     */
    private $type;



    /**
     * These are the associated categories
     *
     * 'argus360',    '360',
     * 'argus360',    'media',
     * 'article',     'post',
     * 'comment',     'post',
     * 'csv',         'document',
     * 'csv',         'media',
     * 'dailymotion', 'embed',
     * 'dailymotion', 'media',
     * 'doc',         'document',
     * 'doc',         'media',
     * 'gif',         'image',
     * 'gif',         'media',
     * 'giphy',       'embed',
     * 'giphy',       'media',
     * 'gist',        'embed',
     * 'gist',        'media',
     * 'jpeg',        'image',
     * 'jpeg',        'media',
     * 'jpg',         'image',
     * 'jpg',         'media',
     * 'message',     'post',
     * 'ods',         'document',
     * 'ods',         'media',
     * 'odt',         'document',
     * 'odt',         'media',
     * 'pdf',         'document',
     * 'pdf',         'media',
     * 'png',         'image',
     * 'png',         'media',
     * 'post',        'post',
     * 'pptx',        'document',
     * 'pptx',        'media',
     * 'thetas',      '360',
     * 'thetas',      'media',
     * 'tweet',       'embed',
     * 'tweet',       'media',
     * 'txt',         'document',
     * 'txt',         'media',
     * 'vimeo',       'embed',
     * 'vimeo',       'media',
     * 'xls',         'document',
     * 'xls',         'media',
     * 'youtube',     'embed',
     * 'youtube',     'media',
     *
     * @var array
     */
    private $assoc = [
        'argus360',    '360',
        'argus360',    'media',
        'article',     'post',
        'comment',     'post',
        'csv',         'document',
        'csv',         'media',
        'dailymotion', 'embed',
        'dailymotion', 'media',
        'doc',         'document',
        'doc',         'media',
        'gif',         'image',
        'gif',         'media',
        'giphy',       'embed',
        'giphy',       'media',
        'gist',        'embed',
        'gist',        'media',
        'jpeg',        'image',
        'jpeg',        'media',
        'jpg',         'image',
        'jpg',         'media',
        'message',     'post',
        'ods',         'document',
        'ods',         'media',
        'odt',         'document',
        'odt',         'media',
        'pdf',         'document',
        'pdf',         'media',
        'png',         'image',
        'png',         'media',
        'post',        'post',
        'pptx',        'document',
        'pptx',        'media',
        'thetas',      '360',
        'thetas',      'media',
        'tweet',       'embed',
        'tweet',       'media',
        'txt',         'document',
        'txt',         'media',
        'vimeo',       'embed',
        'vimeo',       'media',
        'xls',         'document',
        'xls',         'media',
        'youtube',     'embed',
        'youtube',     'media',
    ];

    /**
     * CategoriesService constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Post $post
     *
     * @return Post
     */
    public function setDefaultCategories(Post $post)
    {
        $link = $post->getLink();

        if ($link) {
            $result = parse_url($link);
            // Get type according to url
            if (isset($result['host'])) {
                switch ($result['host']) {
                    case 'www.car360app.com':
                    case 'www.argus360.fr':
                        $this->type = 'argus360';
                        break;
                    case 'dai.ly':
                    case 'www.dailymotion.com':
                        $this->type = 'dailymotion';
                        break;
                    case 'giphy.com':
                        $this->type = 'giphy';
                        break;
                    case 'gist.github.com':
                        $this->type = 'gist';
                        break;
                    case 'twitter.com':
                        $this->type = 'tweet';
                        break;
                    case 'www.youtube.com':
                    case 'youtu.be':
                        $this->type = 'youtube';
                        break;
                    case 'vimeo.com':
                        $this->type = 'vimeo';
                        break;
                }
            }
        }

        $extention = $post->getDocumentExtension();

        if ($extention) {
        }

        for ($i = 0; $i < count($this->assoc); $i = $i + 2) {
            foreach ($post->getCategories() as $category) {
                if ($category->getType == $this->assoc[$i]) {
                    $newCategory = $em->getRepository('AppBundle:Category')->findOneByType($this->assoc[$i + 1]);
                    $post->addCategory($newCategory);
                }
            }
        }

        return $post;
    }
}
