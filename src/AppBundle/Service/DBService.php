<?php

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;

/**
 * Created by PhpStorm.
 * User: MORIN Matthias
 * Date: 04/10/2016
 * Time: 17:34
 */

class DBService
{
    private $db;

    public function __construct(EntityManager $db)
    {
        $this->db = $db;
    }

    public function persist($object)
    {
        $this->db->persist($object);
    }

    public function flush()
    {
        $this->db->flush();
    }

    public function save($object)
    {
        $this->persist($object);
        $this->flush();
    }

    public function remove($object)
    {
        $this->db->remove($object);
    }

    public function repository($entity)
    {
        return $this->db->getRepository($entity);
    }
}
