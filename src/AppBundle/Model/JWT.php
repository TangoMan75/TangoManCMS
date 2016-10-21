<?php

namespace AppBundle\Model;

/**
 * Class JWT
 * @package AppBundle\Model
 */
class JWT
{
    /**
     * @var array
     */
    private $claims;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var bool
     */
    private $signatureValid;

    /**
     * @var bool
     */
    private $expired;

    /**
     * @var bool
     */
    private $beforeValid;


    /**
     * JWT constructor.
     */
    public function __construct()
    {
        $this->claims = [];
        $this->signatureValid = true;
        $this->expired = false;
        $this->beforeValid = true;
    }


    /******************
     * Private claims *
     *****************/

    /**
     * Set token private claim
     *
     * @param $key
     * @param $value
     * @return JWT
     */
    public function set($key, $value)
    {
        $this->claims['private'][$key] = $value;
        return $this;
    }

    /**
     * Get token private claim
     *
     * @return array
     */
    public function get()
    {
        return $this->claims['private'];
    }

    /**
     * Removes token private claim
     *
     * @param $key
     * @return JWT
     */
    public function remove($key)
    {
        unset($this->claims['private'][$key]);
        return $this;
    }


    /*****************
     * Public claims *
     ****************/

    /**
     * Set token public claim
     *
     * @param $key
     * @param $value
     * @return JWT
     */
    public function setPublic($key, $value)
    {
        $this->claims['public'][$key] = $value;
        return $this;
    }

    /**
     * Get token public claim
     *
     * @return array
     */
    public function getPublic()
    {
        return $this->claims['public'];
    }

    /**
     * Removes token public claim
     *
     * @param $key
     * @return JWT
     */
    public function removePublic($key)
    {
        unset($this->claims['public'][$key]);
        return $this;
    }

    /**
     * Get token claims
     *
     * @return array
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /***************************
     * Expiration and validity *
     **************************/

    /**
     * Sets token expiration period
     *
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     * @return JWT
     */
    public function setPeriod(\DateTime $start = null, \DateTime $end = null)
    {
        $this->start = $start;
        $this->claims['public']['start'] = $start->getTimestamp();
        $this->end = $end;
        $this->claims['public']['end'] = $end->getTimestamp();

        return $this;
    }

    /**
     * Set token starting validity from timestamp
     *
     * @param integer $start
     *
     * @return JWT
     */
    public function setStart($start)
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($start);
        $this->start = $datetime;
        $this->claims['public']['start'] = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set token expiration from timestamp
     *
     * @param integer $end
     *
     * @return JWT
     */
    public function setEnd($end)
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($end);
        $this->end = $datetime;
        $this->claims['public']['end'] = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }


    /***********************************************
     * Setters and getters for validation purposes *
     **********************************************/

    /**
     * Set signatureValid
     *
     * @param boolean $signatureValid
     *
     * @return JWT
     */
    public function setSignatureValid($signatureValid)
    {
        $this->signatureValid = $signatureValid;

        return $this;
    }

    /**
     * Get signatureValid
     *
     * @return bool
     */
    public function getSignatureValid()
    {
        return $this->signatureValid;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return JWT
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return bool
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set beforeValid
     *
     * @param boolean $beforeValid
     *
     * @return JWT
     */
    public function setBeforeValid($beforeValid)
    {
        $this->beforeValid = $beforeValid;

        return $this;
    }

    /**
     * Get beforeValid
     *
     * @return bool
     */
    public function getBeforeValid()
    {
        return $this->beforeValid;
    }
}

