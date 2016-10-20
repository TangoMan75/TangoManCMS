<?php

namespace AppBundle\Services;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as Codec;
use Firebase\JWT\SignatureInvalidException;

/**
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
 */
class JWTService
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
    private $signatureStatus;

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
    public function __construct($secret)
    {
        $this->claims = [];

        // Default encryption password taken from Symfony secret parameter
        $this->secret = $secret;

//        $this->setSecret('ThisTokenIsNotSoSecretChangeIt');
    }

    /********************************************************
     * Basic functionality to get and set data inside token *
     *******************************************************/

    /**
     * Set token data
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->claims['data'][$key] = $value;
        return $this;
    }

    /**
     * Get token data
     *
     * @return array
     */
    public function get($key)
    {
        return $this->claims['data'][$key];
    }

    /**
     * Removes token data
     *
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        unset($this->claims['data'][$key]);
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
        $this->claims['exp'] = $end->getTimestamp();
        return $this;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return $this
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
     * @return $this
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

    /**
     * Set token
     *
     * @param string $token
     *
     * @return $this
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
     * Checks token validity
     *
     * @return bool
     */
    public function isSignatureValid()
    {
        return $this->getSignatureStatus();
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

        if ($now < $this->getStart()) {
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

        if ($this->getEnd() != null && $now > $this->getEnd()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Encodes current JWT
     *
     * @return string
     */
    public function encode()
    {
        // Default issuedAt is current time (UNIX timestamp)
        $this->setIssuedAt(time());

        $token = Codec::encode($this->claims, $this->secret);
        $this->token = $token;

        return $token;
    }

    /**
     * Decodes given token
     *
     * @param $token
     */
    public function decode($token)
    {
        try {
            $jwt = Codec::decode($token, $this->secret, ['HS256']);

            $this->setToken($token);

            foreach ($jwt as $key => $value) {
                $this->claims[$key] = $value;
            }

            if (isset($this->claims['exp'])) {
                $end = new \DateTime();
                $this->end = $end->setTimestamp($this->getExpiration());
            }

            if (isset($this->claims['nbf'])) {
                $start = new \DateTime();
                $this->start = $start->setTimestamp($this->getNotBefore());
            } else {
                $this->start = new \DateTime();
            }
        } catch (SignatureInvalidException $e) {
            $this->setSignatureStatus(true);

        } catch (ExpiredException $e) {
            $this->setExpired(true);

        } catch (BeforeValidException $e) {
            $this->setBeforeValid(true);

        } catch(\Exception $e){}
    }



    /******************************************************
     * Custom setters and getters for validation purposes *
     *****************************************************/

    /**
     * Set signatureStatus
     *
     * @param boolean $signatureStatus
     *
     * @return $this
     */
    public function setSignatureStatus($signatureStatus)
    {
        $this->signatureStatus = $signatureStatus;

        return $this;
    }

    /**
     * Get signatureStatus
     *
     * @return bool
     */
    public function getSignatureStatus()
    {
        return $this->signatureStatus;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return $this
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
     * @return $this
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


    /****************************************************
     * Standard RFC 7519 JWT claims setters and getters *
     ***************************************************/

    /**
     * Set issuer
     *
     * @param string $issuer
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setExpiration($expiration)
    {
        $this->claims["exp"] = $expiration;

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
     * @return $this
     */
    public function setNotBefore($notBefore)
    {
        $this->claims["nbf"] = $notBefore;

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
     * @return $this
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
     * @return $this
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
