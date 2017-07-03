<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHaveSites
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 1. Requires `Site` entity to implement `$tags` property with `ManyToMany` and `inversedBy="sites"` annotation.
 * 2. Requires `Site` entity to implement `linkTag` and `unlinkTag` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->sites = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait TagsHaveSites
{
    /**
     * @var array|Site[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Site", mappedBy="tags", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sites = [];

    /**
     * @param array|Site[]|ArrayCollection $sites
     *
     * @return $this
     */
    public function setSites($sites)
    {
        foreach (array_diff($this->sites, $sites) as $site) {
            $this->unlinkSite($site);
        }

        foreach ($sites as $site) {
            $this->linkSite($site);
        }

        return $this;
    }

    /**
     * @return array|Site[]|ArrayCollection $sites
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function addSite(Site $site)
    {
        $this->linkSite($site);
        $site->linkTag($this);

        return $this;
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function removeSite(Site $site)
    {
        $this->unlinkSite($site);
        $site->unlinkTag($this);

        return $this;
    }

    /**
     * @param Site $site
     */
    public function linkSite(Site $site)
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
        }
    }

    /**
     * @param Site $site
     */
    public function unlinkSite(Site $site)
    {
        $this->sites->removeElement($site);
    }
}
