<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\Slugable;
use AppBundle\Entity\Traits\Tagable;
use AppBundle\Entity\Traits\Timestampable;
use AppBundle\Entity\Traits\Publishable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Media
{
    use Slugable;

    use Timestampable;

    use Tagable;

    use Publishable;

    /**
     * @var Integer Media id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var User Media author
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="mediaList")
     */
    private $user;

    /**
     * @var String Media title
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $thumbnail;

    /**
     * @var String Image file name
     * @ORM\Column(type="string", nullable=true)
     */
    private $fileName;

    /**
     * @var String Media link
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="L'URL du media doit être renseigné")
     */
    private $link;

    /**
     * Media constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Slug is generated from title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->setUniqueSlug($title);

        return $this;
    }

    /**
     * Set current date as default title
     * @ORM\PrePersist()
     *
     * @return $this
     */
    public function setDefaultTitle()
    {
        if (!$this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return String
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param String $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        $this->link = '/uploads/'.$fileName;

        return $this;
    }

    /**
     * Delete image file and cached thumbnail
     * @ORM\PreRemove()
     *
     * @return $this
     */
    public function deleteFile()
    {
        switch ($this->type) {
            case 'photo':
            case 'thetas':
                // Get thumbnail path
                $path = __DIR__."/../../../web/media/cache/thumbnail".$this->getLink();
                // Delete file if exists
                if (is_file($path)) {
                    unlink($path);
                }
            case 'document':
                // Get file path
                $path = __DIR__."/../../../web".$this->getLink();
                // Delete file if exists
                if (is_file($path)) {
                    unlink($path);
                }
                break;
        }

        return $this;
    }

    /**
     * @return String
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param String $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        if (stripos($link, '//')) {
            // Remove scheme from url
            $link = ltrim($link, 'http://');
            $link = ltrim($link, 'https://');
            $link = '//'.$link;
        }

        $this->link = $link;
        // On malformed URLs, returns FALSE
        $parsed = parse_url($link);

        if (isset($parsed['host'])) {
            // Automatically set media category according to given url
            switch ($parsed['host']) {
                case 'gist.github.com':
                    $this->setType('gist');
                    break;
                case 'www.youtube.com':
                case 'youtu.be':
                    $this->setType('youtube');
                    $this->thumbnail = '//i.ytimg.com/vi/'.$this->getHash($link).'/hqdefault.jpg';
                    break;
                case 'dai.ly':
                case 'www.dailymotion.com':
                    $this->setType('dailymotion');
                    $this->thumbnail = '//www.dailymotion.com/thumbnail/video/'.$this->getHash($link);
                    break;
                case 'vimeo.com':
                    $this->setType('vimeo');
                    $xml = unserialize(
                        file_get_contents('http://vimeo.com/api/v2/video/'.$this->getHash($link).'.php')
                    );
                    $this->thumbnail = $xml[0]['thumbnail_medium'];
                    break;
                case 'www.car360app.com':
                case 'www.argus360.fr':
                    $this->setType('argus360');
                    // https://car360app.com/viewer/?spin=3e7802c1cd69f08f2a3bae389816ece6&res=640x360&angle=45
                    $this->thumbnail = '//car360app.com/viewer/?spin='.$this->getHash($link).'&res=640x360&angle=45';
                    break;
            }
        }

        return $this;
    }

    /**
     * Generate embed according to category
     *
     * @return String|null
     */
    public function getEmbed()
    {
        if ($this->link) {
            switch ($this->type) {
                case 'gist':
                    return '<script src="//gist.github.com/'.
                        $this->getHash($this->link).
                        '.js"></script>';
                    break;
                case 'argus360':
                    return '<iframe src="//car360app.com/viewer/portable.php?spin='.
                        $this->getHash($this->link).
                        '&res=1920x1080&maximages=-1&frameSize=1920x1080"></iframe>';
                    break;
                case 'youtube':
                    return '<iframe allowfullscreen width="420" height="315" src="//www.youtube.com/embed/'.
                        $this->getHash($this->link).
                        '"></iframe>';
                    break;
                case 'dailymotion':
                    return '<iframe frameborder="0" width="480" height="270" src="//www.dailymotion.com/embed/video/'.
                        $this->getHash($this->link).
                        '?autoplay=0&mute=1" allowfullscreen></iframe>';
                    break;
                case 'vimeo':
                    return '<iframe src="//player.vimeo.com/video/'.
                        $this->getHash($this->link).
                        '?color=ffffff" width="640" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                    break;
                default:
                    return null;
            }
        }

        return null;
    }

    /**
     * Get video hash from url
     *
     * @param $url
     *
     * @return String|null
     */
    private function getHash($url)
    {
        switch (parse_url($url)['host']) {
            case 'gist.github.com':
                // https://gist.github.com/TangoMan75/5bd8b17613e6b6c7a9c80d156c058f96

                return parse_url($url)['path'];
                break;
            case 'www.youtube.com':
                // https://www.youtube.com/watch?v=Vhx_wC1pve8
                parse_str(parse_url($url)['query'], $result);

                return $result['v'];
                break;
            case 'youtu.be':
            case 'vimeo.com':
            case 'dai.ly':
                // https://dai.ly/x97qvv
                // https://youtu.be/Vhx_wC1pve8
                // https://vimeo.com/8914294
                return ltrim(parse_url($url)['path'], '/');
                break;
            case 'www.dailymotion.com':
                // https://www.dailymotion.com/playlist/xy8pc_dankojones77_ultravomit/1#video=x97qvv
                if (strpos(parse_url($url)['path'], 'playlist') !== false) {
                    parse_str(parse_url($url)['fragment'], $result);

                    return $result['video'];
                }

                // https://www.dailymotion.com/video/x5232zo_jaguar-i-pace-concept-2016_auto
                return strstr(ltrim(strrchr(parse_url($url)['path'], '/'), '/'), '_', true);
                break;
            case 'www.argus360.fr':
                // https://www.argus360.fr/viewer/share/3e7802c1cd69f08f2a3bae389816ece6?res=1920x1080&vdp=off
                return ltrim(strrchr(parse_url($url)['path'], '/'), '/');
                break;
            case 'www.car360app.com':
                // https://www.car360app.com/viewer/portable.php?spin=7a8fb5ac7ba0eee31a9e86eff6682bf2&ZoomButtons=off&ZoomScroll=off&TagsList=off&FullscreenButton=off&res=640x360
                parse_str(parse_url($url)['query'], $result);

                return $result['spin'];
                break;
            default:
                return null;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
