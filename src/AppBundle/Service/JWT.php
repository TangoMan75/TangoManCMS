<?php

namespace AppBundle\Service;

use Firebase\JWT\JWT as Codec;
use Symfony\Component\Routing\Router;

/**
 * JWT
 *
 * https://github.com/firebase/php-jwt
 */
class JWT
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $data;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var string
     */
    private $secret;

    public function __construct($secret, Router $router)
    {
        $this->data = [];

        // Default encryption password taken from Symfony secret parameter
        $this->secret = $secret;
    }

    /**
     * Gets specified token data
     *
     * @return string
     */
    public function get($key)
    {
        return $this->data[$key];
    }

    /**
     * Sets given token data
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Unsets given token data
     *
     * @param $key
     * @return $this
     */
    public function unset($key)
    {
        $this->data[$key] = null;
        return $this;
    }

    /**
     * Sets token expiration period
     *
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     * @return $this
     */
    public function setPeriod(\DateTime $start = null, \DateTime $end = null)
    {
        $this->start = $start;
        $this->end = $end;
//        $this->data['exp'] = $end->getTimestamp();
        $this->data['end'] = $end->getTimestamp();
        return $this;
    }

    /**
     * Checks token validity
     *
     * @return bool
     */
    public function isValid()
    {
        if (!empty($this->token)){
            return true;
        } else {
            return false;
        }

    }

    /**
     * Checks token expiration
     *
     * @return bool
     */
    public function isOnTime()
    {
        if ($this->isTooSoon() || $this->isTooLate()){
            return false;
        } else {
            return true;
        }

    }

    /**
     * Checks token expiration
     *
     * @return bool
     */
    public function isTooSoon()
    {
        $now = new \DateTime();

        if ($now < $this->start) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks token expiration
     *
     * @return bool
     */
    public function isTooLate()
    {
        $now = new \DateTime();

        if ($this->end != null && $now > $this->end) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Decyfers given token
     *
     * @param $token
     */
    public function decode($token)
    {
        try {
            $jwt = Codec::decode($token, $this->secret, ['HS256']);
            $this->token = $token;
            foreach ($jwt as $key => $value) {
                $this->data[$key] = $value;
            }

            if (isset($this->data['end'])) {
                $end = new \DateTime();
                $this->end = $end->setTimestamp($this->data['end']);
            }

            if (isset($this->data['start'])) {
                $start = new \DateTime();
                $this->start = $start->setTimestamp($this->data['start']);
            } else {
                $this->start = new \DateTime();
            }
        } catch(\Exception $e){}
    }

    /**
     * Encodes current JWT
     *
     * @return string
     */
    public function encode()
    {
        // Default issuedAt is current time (UNIX timestamp)
//        $this->data['iat'] = time();
        $this->data['start'] = time();

        return Codec::encode($this->data, $this->secret);
    }
}
