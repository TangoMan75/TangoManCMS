<?php

namespace AppBundle\Services;

use AppBundle\Model\JWT;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as Codec;
use Firebase\JWT\SignatureInvalidException;

/**
 * Class JWTService
 * @package AppBundle\Services
 */
class JWTService
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var array
     */
    private $errors;

    /**
     * JWT constructor.
     */
    public function __construct($secret)
    {
        // Default encryption password taken from Symfony secret parameter
        $this->secret = $secret;
        $this->errors = [];
    }

    /**
     * Encodes given JWT
     *
     * @param $jwt JWT
     * @return string
     */
    public function encode(JWT $jwt)
    {
        // Default issuedAt is current time (UNIX timestamp)
        $jwt->setIssuedAt(time());
        $jwt->setSignatureValidity(true);
        $jwt->setExpired(false);
        $jwt->setBeforeValid(false);
        $jwt->setSecret($this->secret);
        $token = Codec::encode($jwt->getClaims(), $this->secret);
        $jwt->setToken($token);
        return $token;
    }

    /**
     * Decodes given token
     *
     * @param $token
     * @return JWT
     */
    public function decode($token)
    {
        $jwt = new JWT();
        $jwt->setToken($token);
        $jwt->setSecret($this->secret);
        $raw = [];
        try {
            // Inits signature and period
            $jwt->setSignatureValidity(true);
            $jwt->setExpired(false);
            $jwt->setBeforeValid(false);
            // Returns object of type stdClass
            $raw = Codec::decode($token, $this->secret, ['HS256']);
            $raw = get_object_vars($raw);
            // Retrieves public claims as defined in RFC7519 specification
            // Will ignore non-standard public claims and private claims that are not in "data" branch
            if (isset($raw['iss'])) {
                $jwt->setIssuer($raw['iss']);
            }
            if (isset($raw['sub'])) {
                $jwt->setSubject($raw['sub']);
            }
            if (isset($raw['aud'])) {
                $jwt->setAudience($raw['aud']);
            }
            if (isset($raw['exp'])) {
                $jwt->setExpiration($raw['exp']);
            }
            if (isset($raw['nbf'])) {
                $jwt->setNotBefore($raw['nbf']);
            }
            if (isset($raw['iat'])) {
                $jwt->setIssuedAt($raw['iat']);
            }
            if (isset($raw['jti'])) {
                $jwt->setJti($raw['jti']);
            }
            // Retrieves private claims from "data" branch
            foreach ($raw['data'] as $key => $value) {
                $jwt->setData($key, $value);
            }
        } catch (SignatureInvalidException $e) {
            $jwt->setSignatureValidity(false);
        } catch (ExpiredException $e) {
            $jwt->setExpired(true);
        } catch (BeforeValidException $e) {
            $jwt->setBeforeValid(true);
        } catch(\Exception $e){}
        return $jwt;
    }

    /********************
     * Token validation *
     *******************/

    /**
     * Checks token validity
     *
     * @param JWT $jwt
     * @return bool
     */
    public function validate(JWT $jwt)
    {
        // Clears errors array
        unset($this->errors);
        $this->errors = [];

        // Checks token signature validity
        $this->isSignatureValid($jwt);

        // Checks token validity period
        $this->isPeriodValid($jwt);

        if (count($this->errors)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns error messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Checks token signature validity
     *
     * @param JWT $jwt
     * @return bool
     */
    public function isSignatureValid(JWT $jwt)
    {
        // Checks token signature validity
        if (!$jwt->getSignatureValidity()) {
            $this->errors['signature'] = "La signature du token n'est pas valide.";
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks token validity period
     *
     * @param JWT $jwt
     * @return bool
     */
    public function isPeriodValid(JWT $jwt)
    {
        if ($this->isBeforeValid($jwt) || $this->isExpired($jwt)){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks if token is before validation period
     *
     * @param JWT $jwt
     * @return bool
     */
    public function isBeforeValid(JWT $jwt)
    {
        $now = new \DateTime();

        if ($now < $jwt->getStart()) {
            $jwt->setBeforeValid(true);
            $this->errors['before'] = "Le token n'est pas encore valide.";
            return true;
        } else {
            $jwt->setBeforeValid(false);
            return false;
        }
    }

    /**
     * Checks token expiration
     *
     * @param JWT $jwt
     * @return bool
     */
    public function isExpired(JWT $jwt)
    {
        $now = new \DateTime();

        if ($jwt->getEnd() != null && $now > $jwt->getEnd()) {
            $jwt->setExpired(true);
            $this->errors['expired'] = "Le token est expiré.";
            return true;
        } else {
            $jwt->setExpired(false);
            return false;
        }
    }
}
