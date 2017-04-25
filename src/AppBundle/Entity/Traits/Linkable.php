<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Linkable
 * 1. Requires entities to own "Timestampable" and "Sluggable" traits.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Linkable
{
    /**
     * @var String
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @return String
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param String $link
     */
    public function setLink($link)
    {
        // Remove scheme from url (doesn't change relative URLs)
        if (stripos($link, '//')) {
            $link = ltrim($link, 'http://');
            $link = ltrim($link, 'https://');
            $link = '//'.$link;
        }

        $this->link = $link;

        // (parse_url function returns false on malformed URLs)
        $parsed = parse_url($link);
        // Sets category according to url
        if (isset($parsed['host'])) {
            // Automatically set media category according to given url
            switch ($parsed['host']) {
                case 'www.youtube.com':
                case 'youtu.be':
                    $this->addCategory('youtube');
                    $this->thumbnail = '//i.ytimg.com/vi/'.$this->getHash($link).'/hqdefault.jpg';
                    break;
                case 'dai.ly':
                case 'www.dailymotion.com':
                    $this->addCategory('dailymotion');
                    $this->thumbnail = '//www.dailymotion.com/thumbnail/video/'.$this->getHash($link);
                    break;
                case 'vimeo.com':
                    $this->addCategory('vimeo');
                    $xml = unserialize(
                        file_get_contents('http://vimeo.com/api/v2/video/'.$this->getHash($link).'.php')
                    );
                    $this->thumbnail = $xml[0]['thumbnail_medium'];
                    break;
                case 'www.car360app.com':
                case 'www.argus360.fr':
                    $this->addCategory('argus360');
                    // https://car360app.com/viewer/?spin=3e7802c1cd69f08f2a3bae389816ece6&res=640x360&angle=45
                    $this->thumbnail = '//car360app.com/viewer/?spin='.$this->getHash($link).'&res=640x360&angle=45';
                    break;
            }
        }
    }

    /**
     * Generate embed according to category
     *
     * @return String|null
     */
    public function getEmbed()
    {
        if ($this->link) {
            if ($this->hasCategory('argus360')) {
                return '<iframe src="//car360app.com/viewer/portable.php?spin='.
                    $this->getHash($this->link).
                    '&res=1920x1080&maximages=-1&frameSize=1920x1080"></iframe>';
            } elseif ($this->hasCategory('youtube')) {
                return '<iframe allowfullscreen width="420" height="315" src="//www.youtube.com/embed/'.
                    $this->getHash($this->link).
                    '"></iframe>';
            } elseif ($this->hasCategory('dailymotion')) {
                return '<iframe frameborder="0" width="480" height="270" src="//www.dailymotion.com/embed/video/'.
                    $this->getHash($this->link).
                    '?autoplay=0&mute=1" allowfullscreen></iframe>';
            } elseif ($this->hasCategory('vimeo')) {
                return '<iframe src="//player.vimeo.com/video/'.
                    $this->getHash($this->link).
                    '?color=ffffff" width="640" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
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
    public function getHash($url)
    {
        switch (parse_url($url)['host']) {
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

                return strstr(ltrim(strrchr(parse_url($url)['path'], '/'), '/'), '_', true);
                break;
            case 'www.argus360.fr':
                // https://www.dailymotion.com/video/x5232zo_jaguar-i-pace-concept-2016_auto
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
}