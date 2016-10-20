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
        try {
            $raw = Codec::decode($token, $this->secret, ['HS256']);
            // Converts token in object
            foreach ($raw as $key => $value) {
                // Retrieves public claims as defined in RFC7519 specification
                switch ($key) {
                    case 'iss':
                        $jwt->setIssuer($value);
                        break;
                    case 'sub':
                        $jwt->setSubject($value);
                        break;
                    case 'aud':
                        $jwt->setAudience($value);
                        break;
                    case 'exp':
                        $jwt->setExpiration($value);
                        break;
                    case 'nbf':
                        $jwt->setNotBefore($value);
                        break;
                    case 'iat':
                        $jwt->setIssuedAt($value);
                        break;
                    case 'jti':
                        $jwt->setJti($value);
                        break;
                    case 'data':
                        // Retrieves private claims from "data" branch
                        foreach ($value as $item => $content) {
                            $jwt->setData($item, $content);
                        }
                        break;
                    default:
                        // Moves private claims to "data" branch
                        $jwt->setData($key, $value);
                        break;
                }
                $jwt->setSignatureValidity(true);
                $jwt->setExpired(false);
                $jwt->setBeforeValid(false);
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
            $this->errors['before'] = "Le token n'est pas encore valide.";
            return true;
        } else {
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
            $this->errors['expired'] = "Le token est expiré.";
            return true;
        } else {
            return false;
        }
    }
}
