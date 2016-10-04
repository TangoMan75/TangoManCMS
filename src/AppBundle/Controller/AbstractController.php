<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractController extends Controller
{
    /**
     * @var
     */
    private $em;

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|EntityManager|object
     */
    protected function em()
    {

        if (!isset($this->em)) {
            $this->em = $this->get('doctrine')->getManager();
        }
        if ($this->em instanceof EntityManager && !$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        return $this->em;
    }

}