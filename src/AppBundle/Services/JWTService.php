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
        $token = Codec::encode($jwt->getClaims(), $this->secret);
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
        try {
            // Returns object of type stdClass
            $raw = Codec::decode($token, $this->secret, ['HS256']);
            // Retrieves public claims
            $jwt->setStart($raw->public->start);
            $jwt->setEnd($raw->public->end);
            // Retrieves private claims
            foreach ($raw->private as $key => $value) {
                $jwt->set($key, $value);
            }
        } catch (SignatureInvalidException $e) {
            $jwt->setSignatureValid(false);
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
        if (!$jwt->getSignatureValid()) {
            $this->errors['signature'] = "La signature du token n'est pas valide.";
        }

        // Checks token validity period
        $now = new \DateTime();
        if ($now < $jwt->getStart()) {
            $jwt->setBeforeValid(true);
            $this->errors['before'] = "Le token n'est pas encore valide.";
        } else {
            $jwt->setBeforeValid(false);
        }

        // Checks token expiration
        if ($jwt->getEnd() != null && $now > $jwt->getEnd()) {
            $jwt->setExpired(true);
            $this->errors['expired'] = "Le token est expiré.";
        } else {
            $jwt->setExpired(false);
        }

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
}
