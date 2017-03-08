<?php

namespace AppBundle\Repository;

class TagRepository extends \Doctrine\ORM\EntityRepository
{
    // /**
    //  * @param  string $name
    //  *
    //  * @return array
    //  */
    // public function findByName($name)
    // {
    //     return $this->createQueryBuilder('tag')
    //                 ->where('tag.name = :name')
    //                 ->setParameter('name', $name)
    //                 ->getQuery()
    //                 ->getResult();
    // }

    // /**
    //  * @param  string $type
    //  *
    //  * @return array
    //  */
    // public function findByName($type)
    // {
    //     return $this->createQueryBuilder('tag')
    //                 ->where('tag.type = :type')
    //                 ->setParameter('type', $type)
    //                 ->getQuery()
    //                 ->getResult();
    // }
}
