<?php

namespace TangoMan\RepositoryHelper;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\RepositoryHelper
 */
trait RepositoryHelper
{
    /**
     * @var null
     */
    private $tableName = null;

    /**
     * @var integer $index
     */
    private $index;

    /**
     * @var array $switches
     */
    private $switches = ['a', 'o', 'r', 'b', 'e', 'l', 'n', 's', 'c', 'p'];

    /**
     * Returns current table name
     *
     * @return string
     */
    public function getTableName()
    {
        if (!$this->tableName) {
            $em = $this->getEntityManager();
            $this->tableName = $em->getClassMetadata($this->getEntityName())->getTableName();
        }

        return $this->tableName;
    }

    /**
     * Returns element count
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function count($criteria = [])
    {
        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);

        $dql->select('COUNT('.$this->getTableName().')');

        return $dql->getQuery()->getSingleScalarResult();
    }

    /**
     * Returns result with pagination (no query support)
     *
     * @param int   $page
     * @param int   $limit
     * @param array $criteria
     *
     * @return Paginator
     */
    public function findAllPaged($page = 1, $limit = 10, $criteria = [])
    {
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

        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);

        $firstResult = ($page - 1) * $limit;
        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($limit);
        $paginator = new Paginator($query);

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        return $paginator;
    }

    /**
     * Returns query result with pagination
     *
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

        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);
        $dql = $this->order($dql, $query);
        $dql = $this->search($dql, $this->cleanQuery($query));
        $dql = $this->join($dql, $query);

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
                throw new NotFoundHttpException('Page not found');
            }
        }

        return $paginator;
    }

    /**
     * Return query as scalar result (for export or API)
     *
     * @param ParameterBag $query
     * @param array        $criteria
     *
     * @return array
     */
    public function findByQueryScalar(ParameterBag $query, $criteria = [])
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 10);

        // QueryBuilder
        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);
        $dql = $this->order($dql, $query);
        $dql = $this->search($dql, $this->cleanQuery($query));
        $dql = $this->join($dql, $query);

        $result = $dql->getQuery()->getScalarResult();
        $offset = ($page - 1) * $limit;

        return array_slice($result, $offset, $limit);
    }

    /**
     * Return all objects as scalar result (no pagination)
     *
     * @param ParameterBag $query
     * @param array        $criteria
     *
     * @return array
     */
    public function export(ParameterBag $query, $criteria = [])
    {
        // QueryBuilder
        $dql = $this->createQueryBuilder($this->getTableName());
        $dql = $this->filter($dql, $criteria);
        $dql = $this->order($dql, $query);
        $dql = $this->search($dql, $this->cleanQuery($query));
        $dql = $this->join($dql, $query);

        return $dql->getQuery()->getScalarResult();
    }

    /**
     * @param QueryBuilder $dql
     * @param array        $criteria
     *
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $dql, $criteria = [])
    {
        $index = 0;
        foreach ($criteria as $param => $value) {
            if (is_array($value)) {
                $dql->andWhere($this->getTableName().'.'.$param.' IN(:filterParam_'.$index.')')
                    ->setParameter(':filterParam_'.$index, array_values($value));
            } else {
                $dql->andWhere($this->getTableName().'.'.$param.' = :filterParam_'.$index)
                    ->setParameter(':filterParam_'.$index, $value);
            }
            $index++;
        }

        return $dql;
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

            if ($params['join']) {
                switch ($params['action']) {
                    // Join count
                    // HIDDEN parameter allows to use statement as orderBy without doctrine fetching undesired data
                    case 'c':
                        $dql->addSelect('COUNT(joinOrder_'.$index.') AS HIDDEN orderParam_'.$index);
                        $dql->leftJoin($params['entity'].'.'.$params['property'], 'joinOrder_'.$index);
                        $orderParam = 'orderParam_'.$index;
                        $groupBy = true;
                        break;

                    // alphabetical order
                    case 'p':
                        $dql->addSelect('joinOrder_'.$index.'.'.$params['property'].' AS HIDDEN orderParam_'.$index);
                        $dql->leftJoin($this->getTableName().'.'.$params['entity'], 'joinOrder_'.$index);
                        $orderParam = 'orderParam_'.$index;
                        $groupBy = true;
                        break;
                }
            } else {
                switch ($params['action']) {
                    // alphabetical order
                    case 'p':
                        $orderParam = $params['entity'].'.'.$params['property'];
                        break;
                }
            }

            if ($orderParam) {
                $dql->addOrderBy($orderParam, $way);
            }
        }

        // groupBy statement should appear only once
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
        $this->index = 0;
        foreach ($query as $search => $value) {

            $params = $this->parse($search);

            if (is_array($value)) {
                // orWhere is default action when using arrays
                $params['action'] = 'o';

                foreach ($value as $multipleSearch) {
                    $dql = $this->searchDql($dql, $params, $multipleSearch);
                }
            } else {
                $dql = $this->searchDql($dql, $params, $value);
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
    public function searchDql(QueryBuilder $dql, $params, $value)
    {
        // Fix boolean bug
        // b : boolean
        // n : not null
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
                if ($params['mode'] == 'a') {
                    $dql->andWhere($params['entity'].'.'.$params['property'].' = :searchParam_'.$this->index);
                } else {
                    $dql->orWhere($params['entity'].'.'.$params['property'].' = :searchParam_'.$this->index);
                }

                $dql->setParameter(':searchParam_'.$this->index, $value);
                $this->index++;
                break;

            // l : like
            case 'l':
                if ($params['mode'] == 'a') {
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
                if ($value === true) {
                    $dql->andWhere($this->getTableName().'.'.$params['property'].' = 1');
                }
                if ($value === false) {
                    $dql->andWhere($this->getTableName().'.'.$params['property'].' = 0');
                }
                break;

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
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function join(QueryBuilder $dql, ParameterBag $query)
    {
        $joins = (array)$query->get('join');
        $this->index = 0;

        foreach ($joins as $index => $value) {
            $dql = $this->joinDql($dql, $this->parse($value));
        }

        return $dql;
    }

    /**
     * @param QueryBuilder $dql
     * @param              $params
     *
     * @return QueryBuilder
     */
    public function joinDql(QueryBuilder $dql, $params)
    {
        if ($params['entity'] == $this->getTableName()) {
            // We have entity: Joining full entity
            $dql->addSelect($params['property'].' AS '.$params['entity'].'_'.$params['property']);
            $dql->leftJoin($params['entity'].'.'.$params['property'], $params['property']);
        } else {
            // We have entity-property: Joining entity-property
            $dql->addSelect($params['entity'].'.'.$params['property'].' AS '.$params['entity'].'_'.$params['property']);
            $dql->leftJoin($this->getTableName().'.'.$params['entity'], $params['entity']);
        }

        $this->index++;

        return $dql;
    }

    /**
     * @param string $string
     * @param string $defaultMode
     *
     * @return array
     */
    public function parse($string, $defaultMode = 'a')
    {
        // Set default values for (mode/action)-entity-property
        // a : mode andWhere
        // o : mode orWhere
        // r : mode orderBy
        // b : action boolean
        // e : action exact match
        // l : action like
        // n : action not null
        // s : action simple array
        // c : action orderBy count
        // p : action orderBy property (alphabetical)
        $params = [
            'join'     => false,
            'mode'     => $defaultMode,
            'action'   => 'l',
            'entity'   => $this->getTableName(),
            'property' => null,
        ];

        // when mode is orderBy, default action is orderBy property (alphabetical)
        // and default property is id
        if ($defaultMode == 'r') {
            $params['action'] = 'p';
            $params['property'] = 'id';
        }

        $temp = explode('-', $string);

        switch (count($temp)) {
            // One parameter only is property
            case 1:
                if ($temp[0]) {
                    $params['property'] = $temp[0];
                }
                break;

            // Two parameters are either "(mode/action) + property" or "entity + property (+ join)"
            case 2:
                $switches = $this->getSwitches($temp[0]);

                // We have switches
                if ($switches) {
                    $mode = $this->getMode($switches);
                    if ($mode) {
                        $params['mode'] = $mode;
                    }

                    $action = $this->getAction($switches);
                    if ($action) {
                        $params['action'] = $action;
                    }
                } else {
                    // No switches then we have "entity + property (+ join)"
                    $params['entity'] = $temp[0];
                }

                $params['property'] = $temp[1];
                break;

            // Three parameters are "(mode/action) + entity + property (+join)"
            case 3:
                $switches = $this->getSwitches($temp[0]);

                if ($switches) {
                    $mode = $this->getMode($switches);
                    if ($mode) {
                        $params['mode'] = $mode;
                    }

                    $action = $this->getAction($switches);
                    if ($action) {
                        $params['action'] = $action;
                    }
                }

                $params['entity'] = $temp[1];
                $params['property'] = $temp[2];
                break;
        }

        // join is true when given entity different from current entity
        if ($params['entity'] != $this->getTableName()) {
            $params['join'] = true;
        }

        // join is true when order and count mode
        if ($params['mode'] == 'r' && $params['action'] == 'c') {
            $params['join'] = true;
        }

        return $params;
    }

    /**
     * @param $string
     *
     * @return array|bool
     */
    public function getSwitches($string)
    {
        $string = strtolower($string);
        $switches = str_split($string, 1);

        // No more than 3 switches allowed (join, mode, action)
        if (count($switches) > 3) {
            return false;
        }

        // Only valid switches allowed
        if (count(array_diff($switches, $this->switches)) > 0) {
            return false;
        }

        return $switches;
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
     * @param array $switches
     *
     * @return null|string
     */
    public function getAction($switches)
    {
        // I left here possibility to have several actions
        $remove = [
            'a',
            'o',
            'r',
        ];

        $action = array_diff($switches, $remove);

        if (count($action) === 0) {
            return null;
        }

        return implode($action);
    }

    /**
     * Removes reserved keywords and empty values from query
     *
     * @param ParameterBag $query
     *
     * @return ParameterBag
     */
    public function cleanQuery(ParameterBag $query)
    {
        // Cloning object to avoid browser side query string override
        $query = clone $query;

        $query->remove('order');
        $query->remove('way');
        $query->remove('limit');
        $query->remove('page');
        $query->remove('join');

        // Remove empty values from query
        $query->replace(
            array_filter(
                $query->all(), function ($value) {
                return $value !== '';
            }
            )
        );

        return $query;
    }

    /**
     * Builds DQL for simple array search
     *
     * @param QueryBuilder $dql
     * @param              $column
     * @param              $search
     *
     * @return QueryBuilder
     */
    public function searchSimpleArray(QueryBuilder $dql, $column, $search)
    {
        $dql->andWhere($this->getTableName().'.'.$column.' LIKE :search')
            ->setParameter(':search', "%,$search,%")
            ->orWhere($this->getTableName().'.'.$column.' = :single')
            ->setParameter(':single', $search)
            ->orWhere($this->getTableName().'.'.$column.' LIKE :start')
            ->setParameter(':start', "$search,%")
            ->orWhere($this->getTableName().'.'.$column.' LIKE :end')
            ->setParameter(':end', "%,$search");

        return $dql;
    }

}
