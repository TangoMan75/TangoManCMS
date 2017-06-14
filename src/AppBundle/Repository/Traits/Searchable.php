<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait Searchable
 * Requires repository to own "TableName" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository\Traits
 */
Trait Searchable
{
    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        // Removes reserved limit and page keywords from query
        $query = clone $query;
        $query->remove('order');
        $query->remove('way');
        $query->remove('limit');
        $query->remove('page');

        // Remove empty values from query
        $query->replace(
            array_filter(
                $query->all(), function ($v) {
                return $v !== "";
            }
            )
        );

        $index = 0;
        foreach ($query as $search => $value) {
            $result = $this->parse($search);

            // Default action is andWhere
            if (!$result['action']) {
                if (!$result['entity']) {
                    $result['action'] = 'a';
                } else {
                    if ($result['entity'] !== $this->getTableName()) {
                        // When entity provided default action is join
                        $result['action'] = 'j';
                    }
                }
            }

            // Default entity is current table
            if (!$result['entity']) {
                $result['entity'] = $this->getTableName();
            }

            // Fix boolean bug
            switch ($value) {
                case 'true':
                    $value = true;
                    break;
                case 'false':
                    $value = false;
                    break;
            }

            switch ($result['action']) {
                case 'a':
                    $dql->andWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
                    $dql->setParameter(':searchParam_'.$index, '%'.$value.'%');
                    $index++;
                    break;
                case 'e':
                    $dql->andWhere($result['entity'].'.'.$result['property'].' = :searchParam_'.$index);
                    $dql->setParameter(':searchParam_'.$index, $value);
                    $index++;
                    break;
                case 'o':
                    $dql->orWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
                    $dql->setParameter(':searchParam_'.$index, '%'.$value.'%');
                    $index++;
                    break;
                case 's':
                    $dql = $this->searchSimpleArray($dql, $result['property'], $value);
                    break;
                case 'j':
                case 'ja':
                    $dql->andWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.'.$result['entity'], $result['entity']);
                    $dql->setParameter(':searchParam_'.$index, '%'.$value.'%');
                    $index++;
                    break;
                case 'je':
                    $dql->andWhere($result['entity'].'.'.$result['property'].' = :searchParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.'.$result['entity'], $result['entity']);
                    $dql->setParameter(':searchParam_'.$index, $value);
                    $index++;
                    break;
                case 'jo':
                    $dql->orWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
                    $dql->leftJoin($this->getTableName().'.'.$result['entity'], $result['entity']);
                    $dql->setParameter(':searchParam_'.$index, '%'.$value.'%');
                    $index++;
                    break;
                case 'n':
                    if ($value === true) {
                        $dql->andWhere($this->getTableName().'.'.$result['property'].' IS NOT NULL');
                    }
                    if ($value === false) {
                        $dql->andWhere($this->getTableName().'.'.$result['property'].' IS NULL');
                    }
                    break;
            }
        }

        return $dql;
    }
}
