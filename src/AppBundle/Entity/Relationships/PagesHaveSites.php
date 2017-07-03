<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PagesHaveSites
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires owned `Site` entity to implement `$pages` property with `ManyToMany` and `mappedBy="sites"` annotation.
 * 2. Requires owned `Site` entity to implement `linkPage` and `unlinkPage` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->sites = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait PagesHaveSites
{
    /**
     * @var array|Site[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Site", inversedBy="pages", cascade={"persist"})
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
     * @return bool
     */
    public function hasSite(Site $site)
    {
        if ($this->sites->contains($site)) {
            return true;
        }

        return false;
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function addSite(Site $site)
    {
        $this->linkSite($site);
        $site->linkPage($this);

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
        $site->unlinkPage($this);

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
