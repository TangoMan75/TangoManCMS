<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    public function testComment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/comment/{id}');
    }

}
