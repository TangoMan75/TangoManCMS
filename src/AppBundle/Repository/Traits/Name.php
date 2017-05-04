<?php

namespace AppBundle\Repository\Traits;

Trait Name
{
    /**
     * @return bool|string
     */
    public function getName()
    {
        $entity = strrchr($this->getEntityName(), '\\');
        $entity = substr($entity, 1, strlen($entity)-1);

        return mb_strtolower($entity);
    }
}
