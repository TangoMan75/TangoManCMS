<?php
/**
 * Created by PhpStorm.
 * User: MORIN Matthias
 * Date: 21/09/2016
 * Time: 17:53
 */

namespace AppBundle\Entity\Repository;


use Doctrine\ORM\EntityRepository;

class Post extends EntityRepository
{
    public function idSuperior($id)
    {
        if(!is_numeric($id)){
            return false;
        }

        $dql = $this->createQueryBuilder('post');

        $dql->andWhere('post.id > :id');
        $dql->setParameter(':id', $id);

        return $dql->getQuery()->getResult();
    }
}
