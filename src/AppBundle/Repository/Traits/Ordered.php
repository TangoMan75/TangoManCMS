<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait Ordered
 * Requires repository to own "TableName" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
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
        $orders = (array)$query->get('order', 'id');
        $ways = (array)$query->get('way', 'DESC');

        $grpb = false;

        foreach ($orders as $index => $order) {

            $way = (isset($ways[$index]) && $ways[$index] == 'ASC') ? 'ASC' : 'DESC';

            switch ($order) {
                // Simplest way to order entities owning base64 avatar or not
                case 'avatar':
                    $dql->addSelect('COUNT('.$this->getTableName().'.avatar) AS orderParam_'.$index);
                    break;

                // Comment count
                case 'comments':
                    $dql->addSelect('COUNT(c_comments) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.comments', 'c_comments');
                    $grpb = true;
                    break;

                // Simplest way to order entities owning image or not
                case 'image':
                    $dql->addSelect('COUNT('.$this->getTableName().'.image) AS orderParam_'.$index);
                    $grpb = true;
                    break;

                // Item count
                case 'items':
                    $dql->addSelect('COUNT(c_items) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.items', 'c_items');
                    $grpb = true;
                    break;

                // Page by title
                case 'page':
                    $dql->addSelect('o_page.title AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.page', 'o_page');
                    break;

                // Page count
                case 'pages':
                    $dql->addSelect('COUNT(c_page) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.pages', 'c_page');
                    $grpb = true;
                    break;

                // Simplest way to order all valid users
                case 'password':
                    $dql->addSelect('COUNT('.$this->getTableName().'.password) AS orderParam_'.$index);
                    $grpb = true;
                    break;

                // Post count
                case 'posts':
                    $dql->addSelect('COUNT(c_post.id) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.posts', 'c_post');
                    $grpb = true;
                    break;

                // Post by title
                case 'post':
                    $dql->addSelect('o_post.title AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.post', 'o_post');
                    break;

                // Section count
                case 'sections':
                    $dql->addSelect('COUNT(c_section.id) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.sections', 'c_section');
                    $grpb = true;
                    break;

                // Tag count
                case 'tags':
                    $dql->addSelect('COUNT(c_tags) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.tags', 'c_tags');
                    $grpb = true;
                    break;

                // User by username
                case 'user':
                    $dql->addSelect('o_user.username AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.user', 'o_user');
                    break;

                // User count
                case 'users':
                    $dql->addSelect('COUNT(c_users) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.users', 'c_users');
                    $grpb = true;
                    break;

                // Vote count
                case 'votes':
                    $dql->addSelect('COUNT(c_votes) AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.votes', 'c_votes');
                    $grpb = true;
                    break;

                default:
                    $dql->addSelect($this->getTableName().'.'.$order.' AS orderParam_'.$index);
                    break;
            }

             $dql->addOrderBy('orderParam_'.$index, $way);
        }

        if ($grpb) {
            $dql->groupBy($this->getTableName());
        }

        return $dql;
    }
}
