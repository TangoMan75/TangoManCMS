<?php

namespace AppBundle\Model;

/**
 * Class JWT
 *
 * RFC 7519 - JSON Web Token (JWT)
 * https://tools.ietf.org/html/rfc7519
 *
 * https://jwt.io
 *
 * iss: Issuer
 * sub: Subject
 * aud: Audience
 * exp: Expiration Time
 * nbf: Not Before
 * iat: Issued At
 * jti: JWT unique identifier ID
 *
 * @package AppBundle\Model
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
    private $signatureValidity;

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
    }


    /******************
     * Private claims *
     *****************/

    /**
     * Set token data
     *
     * @param $key
     * @param $value
     * @return JWT
     */
    public function setData($key, $value)
    {
        $this->claims['data'][$key] = $value;
        return $this;
    }

    /**
     * Get token data
     *
     * @return array
     */
    public function getData($key)
    {
        return $this->claims['data'][$key];
    }

    /**
     * Removes token data
     *
     * @param $key
     * @return JWT
     */
    public function removeData($key)
    {
        unset($this->claims['data'][$key]);
        return $this;
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
        $this->claims['nbf'] = $start->getTimestamp();

        $this->end = $end;
        $this->claims['exp'] = $end->getTimestamp();

        return $this;
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
        $this->claims['nbf'] = $start->getTimestamp();

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
        $this->claims['exp'] = $end->getTimestamp();

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

    /******************************
     * Custom setters and getters *
     *****************************/

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
     * Get claims
     *
     * @return array
     */
    public function getClaims()
    {
        return $this->claims;
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

    /***********************************************
     * Setters and getters for validation purposes *
     **********************************************/

    /**
     * Set signatureValidity
     *
     * @param boolean $signatureValidity
     *
     * @return JWT
     */
    public function setSignatureValidity($signatureValidity)
    {
        $this->signatureValidity = $signatureValidity;

        return $this;
    }

    /**
     * Get signatureValidity
     *
     * @return bool
     */
    public function getSignatureValidity()
    {
        return $this->signatureValidity;
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

    /***************************************
     * Standard RFC 7519 JWT public claims *
     **************************************/

    /**
     * Set issuer
     *
     * @param string $issuer
     *
     * @return JWT
     */
    public function setIssuer($issuer)
    {
        $this->claims["iss"] = $issuer;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return string
     */
    public function getIssuer()
    {
        return $this->claims["iss"];
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
        $this->claims["sub"] = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->claims["sub"];
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
        $this->claims["aud"] = $audience;

        return $this;
    }

    /**
     * Get audience
     *
     * @return string
     */
    public function getAudience()
    {
        return $this->claims["aud"];
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
        $this->claims["exp"] = $expiration;

        // Converts UNIX timestamp into \DateTime
        $end = new \DateTime();
        $this->end = $end->setTimestamp($expiration);

        return $this;
    }

    /**
     * Get expiration
     *
     * @return int
     */
    public function getExpiration()
    {
        return $this->claims["exp"];
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
        $this->claims["nbf"] = $notBefore;

        // Converts UNIX timestamp into \DateTime
        $start = new \DateTime();
        $this->start = $start->setTimestamp($notBefore);

        return $this;
    }

    /**
     * Get notBefore
     *
     * @return int
     */
    public function getNotBefore()
    {
        return $this->claims["nbf"];
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
        $this->claims["iat"] = $issuedAt;

        return $this;
    }

    /**
     * Get issuedAt
     *
     * @return int
     */
    public function getIssuedAt()
    {
        return $this->claims["iat"];
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
        $this->claims["jti"] = $jti;

        return $this;
    }

    /**
     * Get jti
     *
     * @return string
     */
    public function getJti()
    {
        return $this->claims["jti"];
    }
}

