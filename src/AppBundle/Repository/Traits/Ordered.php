<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait Ordered
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
 * @package AppBundle\Repository\Traits
 */
Trait Ordered
{

    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function order(QueryBuilder $dql, ParameterBag $query)
    {
        $order = $query->get('order', 'id');
        $way = $query->get('way', 'DESC');

        switch ($order) {
            // Author by username
            case 'author':
                $dql->addSelect('user.username as orderParam');
                $dql->leftJoin($this->getTableName().'.user', 'user');
                break;

            // Comment count
            case 'comments':
                $dql->addSelect('COUNT(comments) as orderParam');
                $dql->leftJoin($this->getTableName().'.comments', 'comments');
                break;

            // Simplest way to order entities owning image or not
            case 'image':
                $dql->addSelect('COUNT('.$this->getTableName().'.image) as orderParam');
                break;

            // Item count
            case 'items':
                $dql->addSelect('COUNT(items) as orderParam');
                $dql->leftJoin($this->getTableName().'.items', 'items');
                break;

            // Page by title
            case 'page':
                $dql->addSelect('page.title as orderParam');
                $dql->leftJoin($this->getTableName().'.page', 'page');
                break;

            // Simplest way to order all valid users
            case 'password':
                $dql->addSelect('COUNT('.$this->getTableName().'.password) as orderParam');
                break;

            // Post count
            case 'posts':
                $dql->addSelect('COUNT(post.id) as orderParam');
                $dql->leftJoin($this->getTableName().'.posts', 'post');
                break;

            // Section count
            case 'sections':
                $dql->addSelect('COUNT(section.id) as orderParam');
                $dql->leftJoin($this->getTableName().'.sections', 'section');
                break;

            // Tag count
            case 'tags':
                $dql->addSelect('COUNT(tags) as orderParam');
                $dql->leftJoin($this->getTableName().'.tags', 'tags');
                break;

            // User by username
            case 'user':
                $dql->addSelect('user.username as orderParam');
                $dql->leftJoin($this->getTableName().'.user', 'user');
                break;

            // User count
            case 'users':
                $dql->addSelect('COUNT(users) as orderParam');
                $dql->leftJoin($this->getTableName().'.users', 'users');
                break;

            default:
                $dql->addSelect($this->getTableName().'.'.$order.' as orderParam');
                break;
        }

        $dql->groupBy($this->getTableName().'.id');
        $dql->orderBy('orderParam', $way);

        return $dql;
    }
}
