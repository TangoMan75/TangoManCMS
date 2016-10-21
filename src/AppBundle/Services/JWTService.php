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
     * @var JWT
     */
    private $jwt;

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
        $this->jwt = $jwt;
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
        $this->jwt = new JWT();

        try {
            // Returns object of type stdClass
            $raw = Codec::decode($token, $this->secret, ['HS256']);
            // Retrieves start and end public claims other claims are going to be ignored
            $this->jwt->setStart($raw->public->start);
            $this->jwt->setEnd($raw->public->end);
            // Retrieves private claims
            foreach ($raw->private as $key => $value) {
                $this->jwt->set($key, $value);
            }
        } catch (SignatureInvalidException $e) {
            $this->jwt->setSignatureValid(false);
        } catch (ExpiredException $e) {
            $this->jwt->setExpired(true);
        } catch (BeforeValidException $e) {
            $this->jwt->setBeforeValid(true);
        } catch(\Exception $e){}

        return $this->jwt;
    }

    /********************
     * Token validation *
     *******************/

    /**
     * Checks token validity
     *
     * @return bool
     */
    public function validate()
    {
        // Clears errors array
        unset($this->errors);
        $this->errors = [];

        if (!isset($this->jwt)) {
            $this->errors['error'] = "Aucun token n'a été chargé.";
        }

        // Checks token signature validity
        if (!$this->jwt->getSignatureValid()) {
            $this->errors['signature'] = "La signature du token n'est pas valide.";
        }

        // Checks token validity period
        $now = new \DateTime();
        if ($now < $this->jwt->getStart()) {
            $this->jwt->setBeforeValid(true);
            $this->errors['before'] = "Le token n'est pas encore valide.";
        } else {
            $this->jwt->setBeforeValid(false);
        }

        // Checks token expiration
        if ($this->jwt->getEnd() != null && $now > $this->jwt->getEnd()) {
            $this->jwt->setExpired(true);
            $this->errors['expired'] = "Le token est expiré.";
        } else {
            $this->jwt->setExpired(false);
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
