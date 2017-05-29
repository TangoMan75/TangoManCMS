<?php

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Trait Searchable
 * Requires repository to own "TableName" trait.
 * @author  Matthias Morin <tangoman@free.fr>
 *
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
        if ($query->get('category')) {
            $dql = $this->searchSimpleArray($dql, 'category', $query->get('category'));
        }

        if ($query->get('email')) {
            $dql->andWhere($this->getTableName().'.email LIKE :email')
                ->setParameter(':email', '%'.$query->get('email').'%');
        }

        if ($query->get('comment')) {
            $dql->andWhere('comment.title LIKE :comment')
                ->leftJoin($this->getTableName().'.comment', 'comment')
                ->setParameter(':comment', '%'.$query->get('comment').'%');
        }

        if ($query->get('id')) {
            $dql->andWhere($this->getTableName().'.id = :id')
                ->setParameter(':id', $query->get('id'));
        }

        if ($query->get('label')) {
            $dql->andWhere($this->getTableName().'.label LIKE :label')
                ->setParameter(':label', '%'.$query->get('label').'%');
        }

        if ($query->get('link')) {
            $dql->andWhere($this->getTableName().'.link LIKE :link')
                ->setParameter(':link', '%'.$query->get('link').'%');
        }

        if ($query->get('name')) {
            $dql->andWhere($this->getTableName().'.name LIKE :name')
                ->setParameter(':name', '%'.$query->get('name').'%');
        }

        switch ($query->get('password')) {
            case 'true':
                $dql->andWhere($this->getTableName().'.password IS NOT NULL');
                break;
            case 'false':
                $dql->andWhere($this->getTableName().'.password IS NULL');
        }

        if ($query->get('post')) {
            $dql->andWhere('post.title LIKE :post')
                ->leftJoin($this->getTableName().'.post', 'post')
                ->setParameter(':post', '%'.$query->get('post').'%');
        }

        switch ($query->get('published')) {
            case 'true':
                $dql->andWhere($this->getTableName().'.published = :published')
                    ->setParameter(':published', 1);
                break;
            case 'false':
                $dql->andWhere($this->getTableName().'.published = :published')
                    ->setParameter(':published', 0);
        }

        if ($query->get('slug')) {
            $dql->andWhere($this->getTableName().'.slug LIKE :slug')
                ->setParameter(':slug', '%'.$query->get('slug').'%');
        }

        if ($query->get('subtitle')) {
            $dql->andWhere($this->getTableName().'.subtitle LIKE :subtitle')
                ->setParameter(':subtitle', '%'.$query->get('subtitle').'%');
        }

        if ($query->get('summary')) {
            $dql->andWhere($this->getTableName().'.summary LIKE :summary')
                ->setParameter(':summary', '%'.$query->get('summary').'%');
        }

        if ($query->get('s_page')) {
            $dql->andWhere('page.title LIKE :page')
                ->leftJoin($this->getTableName().'.page', 'page')
                ->setParameter(':page', '%'.$query->get('s_page').'%');
        }

        if ($query->get('tag')) {
            $dql->andWhere('tag.name LIKE :tag')
                ->leftJoin($this->getTableName().'.tags', 'tag')
                ->setParameter(':tag', '%'.$query->get('tag').'%');
        }

        if ($query->get('text')) {
            $dql->andWhere($this->getTableName().'.text LIKE :text')
                ->setParameter(':text', '%'.$query->get('text').'%');
        }

        if ($query->get('title')) {
            $dql->andWhere($this->getTableName().'.title LIKE :title')
                ->setParameter(':title', '%'.$query->get('title').'%');
        }

        if ($query->get('type')) {
            $dql->andWhere($this->getTableName().'.type LIKE :type')
                ->setParameter(':type', '%'.$query->get('type').'%');
        }

        if ($query->get('user')) {
            $dql->andWhere('user.username LIKE :user')
//                ->leftJoin($this->getTableName().'.user', 'user')
                ->setParameter(':user', '%'.$query->get('user').'%');
        }

        if ($query->get('username')) {
            $dql->andWhere('user.username LIKE :username')
                ->setParameter(':username', '%'.$query->get('username').'%');
        }

        return $dql;
    }
}
