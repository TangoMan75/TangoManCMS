<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Embeddable
 * 1. Requires entity to own "Categorized" and "HasType" traits.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Embeddable
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

        // Set post type and thumbnail according to url
        if (isset($result['host'])) {
            switch ($result['host']) {
                case 'www.car360app.com':
                case 'www.argus360.fr':
                    $this->type = 'argus360';
                    $this->image = '//car360app.com/viewer/?spin='.$this->getHash($link).'&res=640x360&angle=45';
                    break;
                case 'dai.ly':
                case 'www.dailymotion.com':
                    $this->type = 'dailymotion';
                    $this->image = '//www.dailymotion.com/thumbnail/video/'.$this->getHash($link);
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
                    $this->image = '//i.ytimg.com/vi/'.$this->getHash($link).'/hqdefault.jpg';
                    break;
                case 'vimeo.com':
                    $this->type = 'vimeo';
                    $xml = unserialize(
                        file_get_contents('http://vimeo.com/api/v2/video/'.$this->getHash($link).'.php')
                    );
                    $this->image = $xml[0]['thumbnail_medium'];
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
                case 'dailymotion':
                    return '<iframe frameborder="0" width="480" height="270" src="//www.dailymotion.com/embed/video/'.
                        $this->getHash($this->link).
                        '?autoplay=0&mute=1" allowfullscreen></iframe>';
                    break;
                case 'giphy':
                    return '<iframe src="//giphy.com/embed/'.
                        $this->getHash($this->link).
                        '" width="480" height="270" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>';
                    break;
                case 'gist':
                    return '<script src="//gist.github.com/'.
                        $this->getHash($this->link).
                        '.js"></script>';
                    break;
                case 'tweet':
                    $json = file_get_contents('https://publish.twitter.com/oembed?url=https:'.urlencode($this->link));

                    return json_decode($json, true)['html'];
                    break;
                case 'vimeo':
                    return '<iframe src="//player.vimeo.com/video/'.
                        $this->getHash($this->link).
                        '?color=ffffff" width="640" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                    break;
                case 'youtube':
                    return '<iframe allowfullscreen width="420" height="315" src="//www.youtube.com/embed/'.
                        $this->getHash($this->link).
                        '"></iframe>';
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
            case 'dai.ly':
            case 'vimeo.com':
            case 'youtu.be':
                // https://dai.ly/x97qvv
                // https://vimeo.com/8914294
                // https://youtu.be/Vhx_wC1pve8
                return ltrim(parse_url($url)['path'], '/');
                break;
            case 'www.dailymotion.com':
                // https://www.dailymotion.com/playlist/xpsn5_giorgiopao_tango-argentino/1#video=x5uvup
                if (strpos(parse_url($url)['path'], 'playlist') !== false) {
                    parse_str(parse_url($url)['fragment'], $result);

                    return $result['video'];
                }

                // https://www.dailymotion.com/video/x5232zo_jaguar-i-pace-concept-2016_auto
                // https://www.dailymotion.com/video/x5s849a
                $temp = ltrim(strrchr(parse_url($url)['path'], '/'), '/');
                if (stripos($temp, '_')) {
                    return strstr($temp, '_', true);
                }
                return $temp;
                break;
            case 'giphy.com':
                // https://giphy.com/gifs/030tango-tango-argentine-uGrH3htu5xFW8
                $result = ltrim(strrchr(parse_url($url)['path'], '/'), '/');
                if (strpos($result, '-') !== false) {
                    return ltrim(strrchr($result, '-'), '-');
                }

                return $result;
                break;
            case 'gist.github.com':
            case 'twitter.com':
            case 'www.argus360.fr':
                // https://gist.github.com/vovadocent/7b4a58d7d9e8abb3c68dd82607c2bbf0
                // https://twitter.com/TangoZoneOrg/status/858612556533051392
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