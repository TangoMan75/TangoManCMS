<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TangoMan\RelationshipBundle\Traits\HasRelationships;

/**
 * Vote
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @ORM\Table(name="vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteRepository")
 */
class Vote
{

    use HasRelationships;

    /**
     * @var int
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="votes",
     *                                                      cascade={"persist"})
     */
    private $user;

    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="votes",
     *                                                      cascade={"persist"})
     */
    private $post;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $value;

    /**
     * Vote constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        if ($value >= 0 || $value < 6) {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
