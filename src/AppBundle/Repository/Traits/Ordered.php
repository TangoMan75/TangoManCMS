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
        $groupBy = false;

        foreach ($orders as $index => $order) {
            $way = (isset($ways[$index]) && $ways[$index] == 'ASC') ? 'ASC' : 'DESC';

            $parse = [
                'action'  => null,
                'join'    => null,
                'orderBy' => null,
            ];

            $strTemp = $order;

            if (stripos($order, '_') > 0) {
                $parse['action'] = strstr($order, '_', true);
                $strTemp = ltrim(strstr($order, '_'), '_');
            }

            if (stripos($strTemp, '.') > 0) {
                $parse['join'] = strstr($strTemp, '.', true);
                $parse['orderBy'] = ltrim(strstr($strTemp, '.'), '.');
            } else {
                $parse['orderBy'] = $strTemp;
            }

            // ACTIONS

            if (!$parse['action'] || $parse['action'] == 'o') {
                if ($parse['join']) {
                    $dql->addSelect('o_'.$parse['join'].'.'.$parse['orderBy'].' AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.'.$parse['join'], 'o_'.$parse['join']);
                } else {
                    $dql->addSelect($this->getTableName().'.'.$parse['orderBy'].' AS orderParam_'.$index);
                }
            }

            if ($parse['action'] == 'c') {
                $dql->addSelect('COUNT('.$this->getTableName().'.'.$parse['orderBy'].') AS orderParam_'.$index);
                $groupBy = true;
            }

            if ($parse['action'] == 'j') {
                $dql->addSelect('COUNT(c_'.$parse['orderBy'].') AS orderParam_'.$index);
                $dql->leftJoin($this->getTableName().'.'.$parse['orderBy'], 'c_'.$parse['orderBy']);
                $groupBy = true;
            }

            $dql->addOrderBy('orderParam_'.$index, $way);
        }

        if ($groupBy) {
            $dql->groupBy($this->getTableName());
        }

        return $dql;
    }
}
