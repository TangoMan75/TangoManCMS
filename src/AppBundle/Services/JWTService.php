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
     * JWT constructor.
     */
    public function __construct($secret)
    {
        // Default encryption password taken from Symfony secret parameter
        $this->secret = $secret;
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
        $jwt = new JWT();

        try {
            // Returns object of type stdClass
            $raw = Codec::decode($token, $this->secret, ['HS256']);
            // Retrieves start and end public claims other claims are going to be ignored
            $jwt->setStart(new \DateTime('@'.$raw->public->start));
            $jwt->setEnd(new \Datetime('@'.$raw->public->end));
            // Retrieves private claims
            foreach ($raw->private as $key => $value) {
                $jwt->set($key, $value);
            }
        } catch (SignatureInvalidException $e) {
            $jwt->setSignatureValid(false);
        } catch(\Exception $e){}

        return $jwt;
    }
}
