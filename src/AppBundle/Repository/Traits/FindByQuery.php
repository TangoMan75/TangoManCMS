<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Trait FindByQuery
 * Requires repository to own "RepositoryHelper" trait.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Repository\Traits
 */
Trait FindByQuery
{
    /**
     * @var integer $index
     */
    private $index;

    /**
     * @param ParameterBag $query
     * @param array        $criteria
     *
     * @return Paginator
     * @throws QueryException
     */
    public function findByQuery(ParameterBag $query, $criteria = [])
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 10);

        if (!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if (!is_numeric($limit)) {
            throw new \InvalidArgumentException(
                '$limit must be an integer ('.gettype($limit).' : '.$limit.')'
            );
        }

        // Cloning object to avoid browser side query string override
        $query = clone $query;

        $dql = $this->createQueryBuilder($this->getTableName());

        $dql = $this->filter($dql, $criteria);
        $dql = $this->order($dql, $query);
        $dql = $this->search($dql, $query);

        $firstResult = ($page - 1) * $limit;
        $paginator = new Paginator($dql->getQuery()->setFirstResult($firstResult)->setMaxResults($limit));
        try {
            if (($paginator->count() <= $firstResult) && $page != 1) {
                throw new NotFoundHttpException('Page not found');
            }
        } catch (QueryException $qe) {
            if (function_exists('dump')) {
                throw $qe;
            } else {
                throw new NotFoundHttpException('Page not found'); // or whatever
            }
        }

        return $paginator;
    }

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

            $orderParam = null;
            switch ($result['action']) {
                case 'b':
                    $orderParam = $result['entity'].'.'.$result['property'];
                    break;
                case 'c':
                    $orderParam = 'COUNT('.$result['entity'].'.'.$result['property'].')';
                    $groupBy = true;
                    break;
                case 'j':
                    $orderParam = 'COUNT(c_'.$result['property'].')';
                    $dql->leftJoin($this->getTableName().'.'.$result['property'], 'c_'.$result['property']);
                    $groupBy = true;
                    break;
                case 'jb':
                    $orderParam = 'o_'.$result['entity'].'.'.$result['property'];
                    $dql->leftJoin($this->getTableName().'.'.$result['entity'], 'o_'.$result['entity']);
                    break;
            }

            if ($orderParam) {
                $dql->addOrderBy($orderParam, $way);
            }
        }

        if ($groupBy) {
            $dql->groupBy($this->getTableName());
        }

        return $dql;
    }

    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        // Removes reserved limit and page keywords from query
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

        $this->index = 0;
        foreach ($query as $search => $value) {
            $params = $this->parse($search);

            if (is_array($value)) {
                // orWhere is default action when using arrays
                if (!$params['action']) {
                    $params['action'] = 'o';
                }
                foreach ($value as $multipleSearch) {
                    $dql = $this->buildSearchDql($dql, $params, $multipleSearch);
                }
            } else {
                $dql = $this->buildSearchDql($dql, $params, $value);
            }
        }

        return $dql;
    }

    /**
     * @param QueryBuilder $dql
     * @param              $params
     * @param              $value
     *
     * @return QueryBuilder
     */
    public function buildSearchDql(QueryBuilder $dql, $params, $value)
    {
        // Default action is andWhere
        if (!$params['action']) {
            if (!$params['entity']) {
                $params['action'] = 'a';
            } else {
                if ($params['entity'] !== $this->getTableName()) {
                    // When entity provided default action is join + andWhere
                    $params['action'] = 'j';
                }
            }
        }

        // Default entity is current table
        if (!$params['entity']) {
            $params['entity'] = $this->getTableName();
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

        switch ($params['action']) {
            case 'a':
                $dql->andWhere($params['entity'].'.'.$params['property'].' LIKE :searchParam_'.$this->index);
                $dql->setParameter(':searchParam_'.$this->index, '%'.$value.'%');
                $this->index++;
                break;
            case 'e':
                $dql->andWhere($params['entity'].'.'.$params['property'].' = :searchParam_'.$this->index);
                $dql->setParameter(':searchParam_'.$this->index, $value);
                $this->index++;
                break;
            case 'o':
                $dql->orWhere($params['entity'].'.'.$params['property'].' LIKE :searchParam_'.$this->index);
                $dql->setParameter(':searchParam_'.$this->index, '%'.$value.'%');
                $this->index++;
                break;
            case 's':
                $dql = $this->searchSimpleArray($dql, $params['property'], $value);
                break;
            case 'j':
            case 'ja':
                $dql->andWhere($params['entity'].'.'.$params['property'].' LIKE :searchParam_'.$this->index);
                $dql->leftJoin($this->getTableName().'.'.$params['entity'], $params['entity']);
                $dql->setParameter(':searchParam_'.$this->index, '%'.$value.'%');
                $this->index++;
                break;
            case 'je':
                $dql->andWhere($params['entity'].'.'.$params['property'].' = :searchParam_'.$this->index);
                $dql->leftJoin($this->getTableName().'.'.$params['entity'], $params['entity']);
                $dql->setParameter(':searchParam_'.$this->index, $value);
                $this->index++;
                break;
            case 'jo':
                $dql->orWhere($params['entity'].'.'.$params['property'].' LIKE :searchParam_'.$this->index);
                $dql->leftJoin($this->getTableName().'.'.$params['entity'], $params['entity']);
                $dql->setParameter(':searchParam_'.$this->index, '%'.$value.'%');
                $this->index++;
                break;
            case 'n':
                if ($value === true) {
                    $dql->andWhere($this->getTableName().'.'.$params['property'].' IS NOT NULL');
                }
                if ($value === false) {
                    $dql->andWhere($this->getTableName().'.'.$params['property'].' IS NULL');
                }
                break;
        }

        return $dql;
    }

    /**
     * @return array
     */
    public function parse($string)
    {
        // action_entity_property
        $params = [
            'action'   => null,
            'join'     => null,
            'entity'   => null,
            'property' => null,
        ];

        // a  : andWhere
        // b  : orderBy
        // c  : count
        // e  : exact match
        // j  : join
        // ja : join + andWhere
        // jb : join + orderBy
        // jo : join + orWhere
        // n  : not null
        // o  : orWhere
        // s  : simple array
        $validActions = ['a', 'b', 'c', 'e', 'j', 'ja', 'jb', 'jo', 'n', 'o', 's'];

        $temp = explode('-', $string);

        switch (count($temp)) {
            // One parameter only is property
            case 1:
                $params['property'] = $temp[0];
                break;

            // Two parameters are either "action + property" or "entity + property + join"
            case 2:
                if (in_array($temp[0], $validActions)) {
                    $params['action'] = $temp[0];
                    $params['property'] = $temp[1];
                } else {
                    $params['join'] = true;
                    $params['entity'] = $temp[0];
                    $params['property'] = $temp[1];
                }
                break;

            case 3:
                $params['join'] = true;
                $params['action'] = $temp[0];
                $params['entity'] = $temp[1];
                $params['property'] = $temp[2];
                break;
        }

        return $params;
    }
}
