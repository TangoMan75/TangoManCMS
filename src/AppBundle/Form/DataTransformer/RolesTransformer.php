<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class RolesTransformer
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class RolesTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms array containing role types into an arrayCollection
     *
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
