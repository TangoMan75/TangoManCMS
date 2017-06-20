<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RolesTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface  $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms array containing role types into an arrayCollection
     * @param array $types
     *
     * @return Role[]|array
     */
    public function transform($types)
    {
        return $this->em->getRepository('AppBundle:Role')->findBy(
            [
                'type' => $types,
            ]
        );
    }

    /**
     * @param Role[]|array $roles
     *
     * @return Role[]|array
     */
    public function reverseTransform($roles)
    {
        return $roles;
    }
}