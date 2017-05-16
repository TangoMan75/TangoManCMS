<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PostRepository
 *
 * @package AppBundle\Repository
 */
class PostRepository extends EntityRepository
{
    use Traits\Countable;
    use Traits\SearchableSimpleArray;
    use Traits\TableName;

    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function searchableOrderedPage(ParameterBag $query)
    {
        // Sets default values
        $page = $query->get('page', 1);
        $limit = $query->get('limit', 10);
        $order = $query->get('order', 'modified');
        $way = $query->get('way', 'DESC');

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
            case 'author':
                $dql->addSelect('user.username as orderParam');
                break;

            case 'comments':
                $dql->addSelect('COUNT(comments) as orderParam');
                $dql->leftJoin('post.comments', 'comments');
                break;

            case 'hits':
                $dql->addSelect('post.hits as orderParam');
                break;

            case 'image':
                $dql->addSelect('COUNT(post.image) as orderParam');
                break;

            case 'likes':
                $dql->addSelect('post.likes as orderParam');
                break;

            case 'page':
                $dql->addSelect('page.title as orderParam');
                $dql->leftJoin('post.page', 'page');
                break;

            case 'tags':
                $dql->addSelect('COUNT(tags) as orderParam');
                $dql->leftJoin('post.tags', 'tags');
                break;

            case 'type':
                $dql->addSelect('post.type as orderParam');
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
            $dql->andWhere('post.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('link')) {
            $dql->andWhere('post.link LIKE :link')
                ->setParameter(':link', '%'.$query->get('link').'%');
        }

        switch ($query->get('published')) {
            case 'true':
                $dql->andWhere('post.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere('post.published = :published')
                    ->setParameter(':published', 0);
        }

        if ($query->get('slug')) {
            $dql->andWhere('post.slug LIKE :slug')
                ->setParameter(':slug', '%'.$query->get('slug').'%');
        }

        if ($query->get('s_page')) {
            $dql->andWhere('page.title LIKE :page')
                ->leftJoin('post.page', 'page')
                ->setParameter(':page', '%'.$query->get('s_page').'%');
        }

        if ($query->get('tag')) {
            $dql->andWhere('tag.name LIKE :tag')
                ->leftJoin('post.tags', 'tag')
                ->setParameter(':tag', '%'.$query->get('tag').'%');
        }

        if ($query->get('text')) {
            $dql->andWhere('post.text LIKE :text')
                ->setParameter(':text', '%'.$query->get('text').'%');
        }

        if ($query->get('title')) {
            $dql->andWhere('post.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('title').'%');
        }

        if ($query->get('type')) {
            $dql->andWhere('post.type LIKE :type')
                ->setParameter(':type', '%'.$query->get('type').'%');
        }

        if ($query->get('user')) {
            $dql->andWhere('user.username LIKE :user')
                ->setParameter(':user', '%'.$query->get('user').'%');
        }

        return $dql;
    }

    /**
     * All Posts with joined author email (for export)
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

        $dql = $this->createQueryBuilder('post');
        $dql->orderBy('post.created', 'DESC');

        if ($published) {
            $dql->andWhere('post.published = 1');
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

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->join('post.tags', 'tag')
            ->where('tag = :tag')
            ->setParameter(':tag', $tag)
            ->orderBy('post.created', 'DESC');

        if ($published) {
            $dql->andWhere('post.published = 1');
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

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->where('post.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('post.modified', 'DESC');

        if ($published) {
            $dql->andWhere('post.published = 1');
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

        // Queries user posts
        $dql = $this->createQueryBuilder('post');
        $dql->join('post.user', 'user')
            ->andWhere('user.username = :username')
            ->setParameter(':username', $username)
            ->andWhere('post.published = 1')
            ->orderBy('post.modified', 'DESC');

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
