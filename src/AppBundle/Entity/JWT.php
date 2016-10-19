<?php

namespace AppBundle\Entity;

/**
 * JWT
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
     * @var string
     */
    private $secret;

    /**
     * @var bool
     */
    private $signatureInvalid;

    /**
     * @var bool
     */
    private $expired;

    /**
     * @var bool
     */
    private $beforeValid;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $audience;

    /**
     * @var int
     */
    private $expiration;

    /**
     * @var int
     */
    private $notBefore;

    /**
     * @var int
     */
    private $issuedAt;

    /**
     * @var string
     */
    private $jti;


    /**
     * Set token
     *
     * @param string $token
     *
     * @return JWT
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set claims
     *
     * @param array $claims
     *
     * @return JWT
     */
    public function setClaims($claims)
    {
        $this->claims = $claims;

        return $this;
    }

    /**
     * Get claims
     *
     * @return array
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return JWT
     */
    public function setStart($start)
    {
        $this->start = $start;

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
     * Set end
     *
     * @param \DateTime $end
     *
     * @return JWT
     */
    public function setEnd($end)
    {
        $this->end = $end;

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

    /**
     * Set secret
     *
     * @param string $secret
     *
     * @return JWT
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set signatureInvalid
     *
     * @param boolean $signatureInvalid
     *
     * @return JWT
     */
    public function setSignatureInvalid($signatureInvalid)
    {
        $this->signatureInvalid = $signatureInvalid;

        return $this;
    }

    /**
     * Get signatureInvalid
     *
     * @return bool
     */
    public function getSignatureInvalid()
    {
        return $this->signatureInvalid;
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

    /**
     * Set issuer
     *
     * @param string $issuer
     *
     * @return JWT
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return JWT
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set audience
     *
     * @param string $audience
     *
     * @return JWT
     */
    public function setAudience($audience)
    {
        $this->audience = $audience;

        return $this;
    }

    /**
     * Get audience
     *
     * @return string
     */
    public function getAudience()
    {
        return $this->audience;
    }

    /**
     * Set expiration
     *
     * @param integer $expiration
     *
     * @return JWT
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * Get expiration
     *
     * @return int
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Set notBefore
     *
     * @param integer $notBefore
     *
     * @return JWT
     */
    public function setNotBefore($notBefore)
    {
        $this->notBefore = $notBefore;

        return $this;
    }

    /**
     * Get notBefore
     *
     * @return int
     */
    public function getNotBefore()
    {
        return $this->notBefore;
    }

    /**
     * Set issuedAt
     *
     * @param integer $issuedAt
     *
     * @return JWT
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    /**
     * Get issuedAt
     *
     * @return int
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Set jti
     *
     * @param string $jti
     *
     * @return JWT
     */
    public function setJti($jti)
    {
        $this->jti = $jti;

        return $this;
    }

    /**
     * Get jti
     *
     * @return string
     */
    public function getJti()
    {
        return $this->jti;
    }
}

