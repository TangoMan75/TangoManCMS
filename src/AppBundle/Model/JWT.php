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
     * @var bool
     */
    private $signatureValid;

    /**
     * JWT constructor.
     */
    public function __construct()
    {
        $this->claims = [];
        $this->signatureValid = true;
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
    public function get($key)
    {
        return $this->claims['private'][$key] ?? null;
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

    /**
     * Returns public and private claims
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
        $this->claims['public']['start'] = $start->getTimestamp();
        $this->claims['public']['end'] = $end->getTimestamp();

        return $this;
    }

    /**
     * Set token starting validity from timestamp
     *
     * @param \DateTime $start
     * @return $this
     */
    public function setStart(\DateTime $start)
    {
        $this->claims['public']['start'] = $start->getTimestamp();

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return new \DateTime('@'.$this->claims['public']['start']);
    }

    /**
     * Set token expiration from timestamp
     *
     * @param \DateTime $end
     * @return $this
     */
    public function setEnd(\DateTime $end)
    {
        $this->claims['public']['end'] = $end->getTimestamp();

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return new \DateTime("@".$this->claims['public']['end']);
    }


    /**************
     * Validation *
     *************/

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
     * Checks for signature
     *
     * @return bool
     */
    public function isSignatureValid()
    {
        return $this->signatureValid;
    }

    /**
     * Checks for before valid
     *
     * @return bool
     */
    public function isTooSoon()
    {
        return new \DateTime < new \DateTime("@".$this->claims['public']['start']);
    }

    /**
     * Check for expiration
     *
     * @return bool
     */
    public function isTooLate()
    {
        return new \DateTime > new \DateTime("@".$this->claims['public']['end']);
    }

    /**
     * Check for expiration
     *
     * @return bool
     */
    public function isOnTime()
    {
        return !$this->isTooSoon() && !$this->isTooLate();
    }
}

