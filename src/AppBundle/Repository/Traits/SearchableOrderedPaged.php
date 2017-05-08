<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Trait SearchableOrderedPaged
 * Requires repository to own "TableName" trait.
 *
 * @package AppBundle\Repository\Traits
 */
Trait SearchableOrderedPaged
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function orderedSearchPaged(ParameterBag $query)
    {
        // Sets default values
        $page  = $query->get('page', 1);
        $limit = $query->get('limit', 10);
        $order = $query->get('order', 'modified');
        $way   = $query->get('way', 'DESC');

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

        $dql = $this->createQueryBuilder($this->getTableName);

        // Search
        $dql = $this->search($dql, $query);

        // Order
        $dql = $this->order($dql, $order);



        $dql->leftJoin($this->getTableName.'.user', 'user');
        $dql->groupBy($this->getTableName.'.id');
        $dql->orderBy('orderParam', $way);

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
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        if ($query->get('category')) {
            $dql = $this->searchSimpleArray($dql, 'category', $query->get('category'));
        }

        if ($query->get('id')) {
            $dql->andWhere($this->getTableName.'.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('link')) {
            $dql->andWhere($this->getTableName.'.link LIKE :link')
                ->setParameter(':link', '%'.$query->get('link').'%');
        }

        switch ($query->get('published')) {
            case 'true':
                $dql->andWhere($this->getTableName.'.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere($this->getTableName.'.published = :published')
                    ->setParameter(':published', 0);
        }

        if ($query->get('slug')) {
            $dql->andWhere($this->getTableName.'.slug LIKE :slug')
                ->setParameter(':slug', '%'.$query->get('slug').'%');
        }

        if ($query->get('s_page')) {
            $dql->andWhere('page.title LIKE :page')
                ->leftJoin($this->getTableName.'.page', 'page')
                ->setParameter(':page', '%'.$query->get('s_page').'%');
        }

        if ($query->get('tag')) {
            $dql->andWhere('tag.name LIKE :tag')
                ->leftJoin($this->getTableName.'.tags', 'tag')
                ->setParameter(':tag', '%'.$query->get('tag').'%');
        }

        if ($query->get('text')) {
            $dql->andWhere($this->getTableName.'.text LIKE :text')
                ->setParameter(':text', '%'.$query->get('text').'%');
        }

        if ($query->get('title')) {
            $dql->andWhere($this->getTableName.'.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('title').'%');
        }

        if ($query->get('user')) {
            $dql->andWhere('user.username LIKE :user')
                ->setParameter(':user', '%'.$query->get('user').'%');
        }

        return $dql;
    }

    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function order(QueryBuilder $dql, $order)
    {
        switch ($order) {
            case 'author':
                $dql->addSelect('user.username as orderParam');
                break;

            case 'comments':
                $dql->addSelect('COUNT(comments) as orderParam');
                $dql->leftJoin($this->getTableName.'.comments', 'comments');
                break;

            case 'image':
                $dql->addSelect('COUNT('.$this->getTableName.'.image) as orderParam');
                break;

            case 'page':
                $dql->addSelect('page.title as orderParam');
                $dql->leftJoin($this->getTableName.'.page', 'page');
                break;

            case 'tags':
                $dql->addSelect('COUNT(tags) as orderParam');
                $dql->leftJoin($this->getTableName.'.tags', 'tags');
                break;

            default:
                $dql->addSelect($this->getTableName.'.'.$order.' as orderParam');
                break;
        }

        return $dql;
    }

    /**
     * All Posts with joined author email (for export)
     */
    public function findAllPosts()
    {
        return $this->createQueryBuilder($this->getTableName)
            ->leftJoin($this->getTableName.'.user', 'user')
            ->addSelect('user.email AS user_email')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * Posts pagination
     *
     * @param int $page
     * @param int $limit
     *
     * @return Paginator
     */
    public function findAllPaged($page = 1, $limit = 10, $published = true)
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

        $dql = $this->createQueryBuilder($this->getTableName);
        $dql->orderBy($this->getTableName.'.modified', 'DESC');

        if ($published) {
            $dql->andWhere($this->getTableName.'.published = 1');
        }

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
     * Post pagination by tag
     *
     * @param Tag $tag
     * @param int $page
     * @param int $limit
     *
     * @return Paginator
     */
    public function findByTagPaged(Tag $tag, $page = 1, $limit = 10, $published = true)
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

        // Queries user '.$this->getTableName.''s
        $dql = $this->createQueryBuilder($this->getTableName);
        $dql->join($this->getTableName.'.tags', 'tag')
            ->where('tag = :tag')
            ->setParameter(':tag', $tag)
            ->orderBy($this->getTableName.'.created', 'DESC');

        if ($published) {
            $dql->andWhere($this->getTableName.'.published = 1');
        }

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
     * Post pagination by user
     *
     * @param User $user
     * @param int  $page
     * @param int  $limit
     *
     * @return Paginator
     */
    public function findByUserPaged(User $user, $page = 1, $limit = 10, $published = true)
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

        // Queries user '.$this->getTableName.''s
        $dql = $this->createQueryBuilder($this->getTableName);
        $dql->where($this->getTableName.'.user = :user')
            ->setParameter(':user', $user)
            ->orderBy($this->getTableName.'.modified', 'DESC');

        if ($published) {
            $dql->andWhere($this->getTableName.'.published = 1');
        }

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
     * Post pagination by username
     *
     * @param string $username
     * @param int    $page
     * @param int    $limit
     *
     * @return Paginator
     */
    public function findByUsernamePaged($username, $page = 1, $limit = 10)
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

        // Queries user '.$this->getTableName.''s
        $dql = $this->createQueryBuilder($this->getTableName);
        $dql->join($this->getTableName.'.user', 'user')
            ->andWhere('user.username = :username')
            ->setParameter(':username', $username)
            ->andWhere($this->getTableName.'.published = 1')
            ->orderBy($this->getTableName.'.modified', 'DESC');

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
}
