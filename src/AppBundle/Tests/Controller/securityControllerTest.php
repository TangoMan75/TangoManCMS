<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class securityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    }

    public function testLogout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logout');
    }

    public function testCheck()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/check');
    }

    public function testConfirm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/confirm');
    }

    public function testReset()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reset');
    }

}
