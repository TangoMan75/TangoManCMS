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
     * j : join
     * a : mode andWhere
     * o : mode orWhere
     * r : mode orderBy
     * b : action boolean
     * e : action exact match
     * l : action like
     * n : action not null
     * s : action simple array
     * c : action orderBy count
     * p : action orderBy property
     *
     * @var array $switches
     */
    private $switches = ['a', 'b', 'c', 'e', 'j', 'l', 'n', 'o', 'p', 's'];

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
        $orders = (array)$query->get('order');
        $ways = (array)$query->get('way');
        $groupBy = false;

        foreach ($orders as $index => $order) {
            // 'DESC' is default way
            $way = (isset($ways[$index]) && $ways[$index] == 'ASC') ? 'ASC' : 'DESC';

            $params = $this->parse($order, 'r');

            $orderParam = null;
            switch ($params['action']) {

                // Default orderParam
                case 'p':
                    $orderParam = $params['entity'].'.'.$params['property'];
                    break;

                // order count
                case 'c':
                    $orderParam = 'COUNT('.$params['entity'].'.'.$params['property'].')';
                    $groupBy = true;
                    break;

                // Join count
                case 'j':
                    $orderParam = 'COUNT(c_'.$params['property'].')';
                    $dql->leftJoin($this->getTableName().'.'.$params['property'], 'c_'.$params['property']);
                    $groupBy = true;
                    break;

                case 'jb':
                    $orderParam = 'o_'.$params['entity'].'.'.$params['property'];
                    $dql->leftJoin($this->getTableName().'.'.$params['entity'], 'o_'.$params['entity']);
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
                return $v !== '';
            }
            )
        );

        $this->index = 0;
        foreach ($query as $search => $value) {

            $params = $this->parse($search);

            if (is_array($value)) {
                // orWhere is default action when using arrays
                $params['action'] = 'o';

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
        // Fix boolean bug
        if ($params['action'] == 'b' || $params['action'] == 'n') {
            switch ($value) {
                case 'true':
                    $value = true;
                    break;
                case 'false':
                    $value = false;
                    break;
            }
        }

        switch ($params['action']) {
            // e  : exact match
            case 'e':
                if ($params['mode'] = 'a') {
                    $dql->andWhere($params['entity'].'.'.$params['property'].' = :searchParam_'.$this->index);
                } else {
                    $dql->orWhere($params['entity'].'.'.$params['property'].' = :searchParam_'.$this->index);
                }

                $dql->setParameter(':searchParam_'.$this->index, $value);
                $this->index++;
                break;

            // l : like
            case 'l':
                if ($params['mode'] = 'a') {
                    $dql->andWhere($params['entity'].'.'.$params['property'].' LIKE :searchParam_'.$this->index);
                } else {
                    $dql->orWhere($params['entity'].'.'.$params['property'].' LIKE :searchParam_'.$this->index);
                }

                $dql->setParameter(':searchParam_'.$this->index, '%'.$value.'%');
                $this->index++;
                break;

            // s  : simple array
            case 's':
                $dql = $this->searchSimpleArray($dql, $params['property'], $value);
                break;

            // b : boolean
            case 'b':
                // n : not null
            case 'n':
                if ($value === true) {
                    $dql->andWhere($this->getTableName().'.'.$params['property'].' IS NOT NULL');
                }
                if ($value === false) {
                    $dql->andWhere($this->getTableName().'.'.$params['property'].' IS NULL');
                }
                break;
        }

        if ($params['join'] == true) {
            $dql->leftJoin($this->getTableName().'.'.$params['entity'], $params['entity']);
        }

        return $dql;
    }

    /**
     * @return array
     */
    public function parse($string, $defaultMode = 'a')
    {
        // Set default values for action-entity-property
        // join   : true / false
        // mode   : andWhere / orWhere / orderBy
        // action : boolean / count / exact match / not null / simple array
        $params = [
            'join'     => false,
            'mode'     => $defaultMode,
            'action'   => 'l',
            'entity'   => $this->getTableName(),
            'property' => null,
        ];

        if ($defaultMode == 'r') {
            $params['action'] = 'r';
        }

        $temp = explode('-', $string);

        switch (count($temp)) {
            // One parameter only is property
            case 1:
                $params['property'] = $temp[0];
                break;

            // Two parameters are either "action + property" or "entity + property (+ join)"
            case 2:
                $switches =$this->getSwitches($temp[0]);
                if ($switches) {
                    $params['action'] = $this->getAction($switches);
                    $params['property'] = $temp[1];
                } else {
                    $params['join'] = true;
                    $params['entity'] = $temp[0];
                    $params['property'] = $temp[1];
                }
                break;

            // Three parameters are "action + entity + property (+join)"
            case 3:
                if ($params['entity'] != $temp[1]) {
                    $params['join'] = true;
                }
                $params['action'] = $temp[0];
                $params['entity'] = $temp[1];
                $params['property'] = $temp[2];
                break;
        }

        // andWhere is default action when join is true
        if (stripos($params['action'], 'j') === 0) {
            $params['join'] = true;
            if ($params['action'] == 'j') {
                $params['action'] = $defaultAction;
            } else {
                $params['action'] = str_split($params['action'], 1)[1];
            }
        }

        die(dump($params));

        return $params;
    }

    /**
     * @param array $switches
     *
     * @return bool
     */
    public function getJoin($switches)
    {
        if (in_array('j', $switches)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $switches
     *
     * @return array
     */
    public function getAction($switches)
    {
        $remove = [
            'a',
            'j',
            'o',
            'r',
        ];

        return array_diff($switches, $remove);
    }

    /**
     * @param array $switches
     *
     * @return string|null
     */
    public function getMode($switches)
    {
        if (in_array('a', $switches)) {
            return 'a';
        }

        if (in_array('o', $switches)) {
            return 'o';
        }

        if (in_array('r', $switches)) {
            return 'r';
        }

        return null;
    }

    /**
     * @param $string
     *
     * @return array|bool
     */
    public function getSwitches($string)
    {
        $switches = str_split($string, 1);

        // No more than 3 switches allowed
        if (count($switches) > 3) {
            return false;
        }

        // Only valid switches allowed
        if (count(array_diff($switches, $this->switches)) === 0) {
            return false;
        }

        return $switches;
    }
}
