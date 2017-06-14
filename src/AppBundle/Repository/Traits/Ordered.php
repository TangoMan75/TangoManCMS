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

            $result = $this->parse($order);

            // Default action is orderBy
            if (!$result['action']) {
                if (!$result['entity']) {
                    $result['action'] = 'b';
                } else {
                    if ($result['entity'] !== $this->getTableName()) {
                        // When entity provided default action is join
                        $result['action'] = 'jb';
                    }
                }
            }

            // Default entity is current table
            if (!$result['entity']) {
                $result['entity'] = $this->getTableName();
            }

            switch ($result['action']) {
                case 'b':
                    $dql->addSelect($result['entity'].'.'.$result['property'].' AS orderParam_'.$index);
                    break;
                case 'c':
                    $dql->addSelect('COUNT('.$result['entity'].'.'.$result['property'].') AS orderParam_'.$index);
                    $groupBy = true;
                    break;
                case 'j':
                    $dql->addSelect('COUNT(c_'.$result['property'].') AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.'.$result['property'], 'c_'.$result['property']);
                    $groupBy = true;
                    break;
                case 'jb':
                    $dql->addSelect('o_'.$result['entity'].'.'.$result['property'].' AS orderParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.'.$result['entity'], 'o_'.$result['entity']);
                    break;
            }

            $dql->addOrderBy('orderParam_'.$index, $way);
        }

        if ($groupBy) {
            $dql->groupBy($this->getTableName());
        }

        return $dql;
    }
}
