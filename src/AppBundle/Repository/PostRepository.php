<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostRepository extends EntityRepository
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function sortedSearchPaged(ParameterBag $query)
    {
        // Sets default values
        $page  = $query->get('page', 1);
        $limit = $query->get('limit', 10);
        $order = $query->get('order', 'title');
        $way   = $query->get('way', 'ASC');

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

        $dql = $this->createQueryBuilder('post');

        // Search inside id, title, subtitle and content columns
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'comments':
                $dql->addSelect('COUNT(comment) as orderParam');
                $dql->leftJoin('post.comments', 'comment');
                break;

            case 'author':
                $dql->addSelect('user.username as orderParam');
                break;

            default:
                $dql->addSelect('post.'.$order.' as orderParam');
                break;
        }

        $dql->leftJoin('post.user', 'user');
        $dql->groupBy('post.id');
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
     * All Posts with joins
     */
    public function findAllPosts()
    {
        return $this->createQueryBuilder('post')
                    ->leftJoin('post.user', 'user')
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
    public function findAllPaged($page = 1, $limit = 10)
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

        $dql = $this->createQueryBuilder('post');
        $dql->orderBy('post.dateCreated', 'DESC');

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
    public function findByTagPaged(Tag $tag, $page = 1, $limit = 10)
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

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->join('post.tags', 'tag')
            ->where('tag = :tag')
            ->setParameter(':tag', $tag)
            ->orderBy('post.dateCreated', 'DESC');

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
    public function findByUserPaged(User $user, $page = 1, $limit = 10)
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

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->where('post.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('post.dateCreated', 'DESC');

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

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->join('post.user', 'user')
            ->andWhere('user.username = :username')
            ->setParameter(':username', $username)
            ->orderBy('post.dateCreated', 'DESC');

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
     * Get post count
     * @return int $count post count
     */
    public function count()
    {
        return $this->createQueryBuilder('post')
            ->select('COUNT(post)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $dql
     * @param ParameterBag $query
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $dql, ParameterBag $query)
    {
        if ($query->get('s_id')) {
            $dql->andWhere('post.id = :id')
                ->setParameter(':id', $query->get('s_id'));
        }

        if ($query->get('s_title')) {
            $dql->andWhere('post.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('s_title').'%');
        }

        if ($query->get('s_subtitle')) {
            $dql->andWhere('post.subtitle LIKE :subtitle')
                ->setParameter(':subtitle', '%'.$query->get('s_subtitle').'%');
        }

        if ($query->get('s_content')) {
            $dql->andWhere('post.content LIKE :content')
                ->setParameter(':content', '%'.$query->get('s_content').'%');
        }

        if ($query->get('s_user')) {
            $dql->andWhere('user.username LIKE :user')
                ->setParameter(':user', '%'.$query->get('s_user').'%');
        }

        if ($query->get('s_tag')) {
            $dql->andWhere('tag.name LIKE :tag')
                ->leftJoin('post.tags', 'tag')
                ->setParameter(':tag', '%'.$query->get('s_tag').'%');
        }

        return $dql;
    }
}
