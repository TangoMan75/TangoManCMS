<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaRepository extends EntityRepository
{
    /**
     * @param ParameterBag $query
     *
     * @return Paginator
     */
    public function sortedSearchPaged(ParameterBag $query)
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

        $dql = $this->createQueryBuilder('media');

        // Search inside id, title, subtitle and content columns
        $dql = $this->search($dql, $query);

        // Order according to ownership count
        switch ($order) {
            case 'comments':
                $dql->addSelect('COUNT(comments) as orderParam');
                $dql->leftJoin('media.comments', 'comments');
                break;

            case 'tags':
                $dql->addSelect('COUNT(tags) as orderParam');
                $dql->leftJoin('media.tags', 'tags');
                break;

            case 'author':
                $dql->addSelect('user.username as orderParam');
                break;

            case 'page':
                $dql->addSelect('page.title as orderParam');
                $dql->leftJoin('media.page', 'page');
                break;

            default:
                $dql->addSelect('media.'.$order.' as orderParam');
                break;
        }

        $dql->leftJoin('media.user', 'user');
        $dql->groupBy('media.id');
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
        if ($query->get('s_id')) {
            $dql->andWhere('media.id = :id')
                ->setParameter(':id', $query->get('s_id'));
        }

        if ($query->get('s_title')) {
            $dql->andWhere('media.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('s_title').'%');
        }

        if ($query->get('s_subtitle')) {
            $dql->andWhere('media.subtitle LIKE :subtitle')
                ->setParameter(':subtitle', '%'.$query->get('s_subtitle').'%');
        }

        if ($query->get('s_content')) {
            $dql->andWhere('media.content LIKE :content')
                ->setParameter(':content', '%'.$query->get('s_content').'%');
        }

        if ($query->get('s_user')) {
            $dql->andWhere('user.username LIKE :user')
                ->setParameter(':user', '%'.$query->get('s_user').'%');
        }

        if ($query->get('s_page')) {
            $dql->andWhere('page.title LIKE :page')
                ->setParameter(':page', '%'.$query->get('s_page').'%');
        }

        switch ($query->get('s_published')) {
            case 'true':
                $dql->andWhere('media.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere('media.published = :published')
                    ->setParameter(':published', 0);
        }

        if ($query->get('s_tag')) {
            $dql->andWhere('s_tag.name LIKE :tag')
                ->leftJoin('media.tags', 's_tag')
                ->setParameter(':tag', '%'.$query->get('s_tag').'%');
        }

        return $dql;
    }

    /**
     * All Medias with joined author email
     */
    public function findAllMedias()
    {
        return $this->createQueryBuilder('media')
            ->leftJoin('media.user', 'user')
            ->addSelect('user.email AS user_email')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * Medias pagination
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

        $dql = $this->createQueryBuilder('media');
        $dql->orderBy('media.modified', 'DESC');

        if ($published) {
            $dql->andWhere('media.published = 1');
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
     * Media pagination by tag
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

        // Queries user medias
        $dql = $this->createQueryBuilder('media');
        $dql->join('media.tags', 'tag')
            ->where('tag = :tag')
            ->andWhere('media.published = 1')
            ->setParameter(':tag', $tag)
            ->orderBy('media.created', 'DESC');

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
     * Media pagination by user
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

        // Queries user medias
        $dql = $this->createQueryBuilder('media');
        $dql->where('media.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('media.modified', 'DESC');

        if ($published) {
            $dql->andWhere('media.published = 1');
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
     * Media pagination by username
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

        // Queries user medias
        $dql = $this->createQueryBuilder('media');
        $dql->join('media.user', 'user')
            ->andWhere('user.username = :username')
            ->setParameter(':username', $username)
            ->andWhere('media.published = 1')
            ->orderBy('media.modified', 'DESC');

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
     * Get media count
     *
     * @return int $count media count
     */
    public function count()
    {
        return $this->createQueryBuilder('media')
            ->select('COUNT(media)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
