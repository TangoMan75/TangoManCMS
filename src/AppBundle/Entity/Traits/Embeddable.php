<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait Embeddable
 * 1. Requires entities to own "Categorized" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Embeddable
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
     *
     * @return $this
     */
    public function setLink($link)
    {
        // (parse_url function returns false on malformed URLs)
        $result = parse_url($link);

        // Remove scheme from url (doesn't change relative URLs)
        $this->link = '//'.
            (isset($result['user']) ? $result['user'] : '').
            (isset($result['pass']) ? ':'.$result['pass'].'@' : '').
            $result['host'].
            (isset($result['port']) ? ':'.$result['port'] : '').
            (isset($result['path']) ? $result['path'] : '').
            (isset($result['query']) ? '?'.$result['query'] : '').
            (isset($result['fragment']) ? '#'.$result['fragment'] : '');

        // Sets type according to url
        if (isset($result['host'])) {
            // Automatically set post type according to given url
            switch ($result['host']) {
                case 'gist.github.com':
                    $this->setType('gist');
                    break;
                case 'www.youtube.com':
                case 'youtu.be':
                    $this->setType('youtube');
                    $this->image = '//i.ytimg.com/vi/'.$this->getHash($link).'/hqdefault.jpg';
                    break;
                case 'dai.ly':
                case 'www.dailymotion.com':
                    $this->setType('dailymotion');
                    $this->image = '//www.dailymotion.com/thumbnail/video/'.$this->getHash($link);
                    break;
                case 'vimeo.com':
                    $this->setType('vimeo');
                    $xml = unserialize(
                        file_get_contents('http://vimeo.com/api/v2/video/'.$this->getHash($link).'.php')
                    );
                    $this->image = $xml[0]['thumbnail_medium'];
                    break;
                case 'www.car360app.com':
                case 'www.argus360.fr':
                    $this->setType('argus360');
                    // //car360app.com/viewer/?spin=3e7802c1cd69f08f2a3bae389816ece6&res=640x360&angle=45
                    $this->image = '//car360app.com/viewer/?spin='.$this->getHash($link).'&res=640x360&angle=45';
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
                case 'argus360':
                    return '<iframe src="//car360app.com/viewer/portable.php?spin='.
                        $this->getHash($this->link).
                        '&res=1920x1080&maximages=-1&frameSize=1920x1080"></iframe>';
                    break;
                case 'gist':
                    return '<script src="//gist.github.com/'.
                        $this->getHash($this->link).
                        '.js"></script>';
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
                // https://www.dailymotion.com/playlist/xpsn5_giorgiopao_tango-argentino/1#video=x5uvup
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