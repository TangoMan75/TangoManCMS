<?php

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;

class EntityManagerService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * EntityManagerService constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Persists entity.
     *
     * @param $object
     */
    public function persist($object)
    {
        $this->em->persist($object);
    }

    /**
     * Flushes entity.
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * Persists and flushes data.
     *
     * @param $object
     */
    public function save($object)
    {
        $this->persist($object);
        $this->flush();
    }

    /**
     * Removes data.
     *
     * @param $object
     */
    public function remove($object)
    {
        $this->em->remove($object);
    }

    /**
     * Gets entity repository.
     *
     * @param $entity
     * @return \Doctrine\ORM\EntityRepository
     */
    public function repository($entity)
    {
        return $this->em->getRepository($entity);
    }
}
