<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\User;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Entity\Stat;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait StatHasItems
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires `Item` entity to implement `$stats` property with `ManyToMany` and `inversedBy="items"` annotation.
 * 2. Requires `Item` entity to implement `linkStat` and `unlinkStat` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->items = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait StatHasItems
{
    /**
     * @var User[]|Post[]|Page[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="stat", cascade={"persist"})
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="stat", cascade={"persist"})
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="stat", cascade={"persist"})
     */
    private $items = [];

    /**
     * @param array|ArrayCollection $items
     *
     * @return $this|Stat
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return array|ArrayCollection $items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasItem($item)
    {
        if (in_array($item, (array)$this->items)) {
            return true;
        }

        return false;
    }

    /**
     * @return $this|Stat
     */
    public function addItem($item)
    {
        $this->linkItem($item);
        $item->linkStat($this);

        return $this;
    }

    /**
     * @return $this|Stat
     */
    public function removeItem($item)
    {
        $this->unlinkItem($item);
        $item->unlinkStat();

        return $this;
    }

    public function linkItem($item)
    {
        if (!in_array($item, (array)$this->items)) {
            $this->items[] = $item;
        }
    }

    public function unlinkItem($item)
    {
        $this->items->removeElement($item);
    }
}
