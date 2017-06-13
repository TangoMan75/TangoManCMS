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
//        if ($query->get('category')) {
//            $dql = $this->searchSimpleArray($dql, 'categories', $query->get('category'));
//        }

        // Remove empty values from query
        $query->replace(array_filter($query->all(), function($v){
            return $v !== "";
        }));

        $index = 0;
        foreach ($query as $search => $value) {
            $result = $this->parse($search);

//            die(dump($result));

            if (!$result['action']) {
                // Default action is andWhere
                $result['action'] = 'a';
            }

            if (!$result['entity']) {
                // Default entity is current table name
                $result['entity'] = $this->getTableName();
            }

            if ($result['action'] == 'a') {
                $dql->andWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
            } else {
                $dql->orWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
            }
            $dql->setParameter(':searchParam_'.$index, '%'.$query->get($search).'%');
            $index++;

            if ($result['action'] == 'ja' || $result['action'] == 'jo') {
                if ($result['action'] == 'ja') {
                    $dql->andWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
                } elseif ($result['action'] == 'jo') {
                    $dql->orWhere($result['entity'].'.'.$result['property'].' LIKE :searchParam_'.$index);
                }
                $dql->leftJoin($this->getTableName().'.'.$result['entity'], $result['entity']);
                $dql->setParameter(':searchParam_'.$index, '%'.$query->get($search).'%');
                $index++;
            }
        };

//        die(dump($dql->getQuery()));
//
//        if ($query->get('email')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.email LIKE :email');
//            } else {
//                $dql->andWhere($this->getTableName().'.email LIKE :email');
//            }
//            $dql->setParameter(':email', '%'.$query->get('email').'%');
//        }
//
//        if ($query->get('comment')) {
//            if ($orWhere) {
//                $dql->orWhere('comment.title LIKE :comment');
//            } else {
//                $dql->andWhere('comment.title LIKE :comment');
//            }
//            $dql->leftJoin($this->getTableName().'.comment', 'comment');
//            $dql->setParameter(':comment', '%'.$query->get('comment').'%');
//        }
//
//        if ($query->get('id')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.id = :id');
//            } else {
//                $dql->andWhere($this->getTableName().'.id = :id');
//            }
//            $dql->setParameter(':id', $query->get('id'));
//        }
//
//        if ($query->get('label')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.label LIKE :label');
//            } else {
//                $dql->andWhere($this->getTableName().'.label LIKE :label');
//            }
//            $dql->setParameter(':label', '%'.$query->get('label').'%');
//        }
//
//        if ($query->get('link')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.link LIKE :link');
//            } else {
//                $dql->andWhere($this->getTableName().'.link LIKE :link');
//            }
//            $dql->setParameter(':link', '%'.$query->get('link').'%');
//        }
//
//        if ($query->get('name')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.name LIKE :name');
//            } else {
//                $dql->andWhere($this->getTableName().'.name LIKE :name');
//            }
//            $dql->setParameter(':name', '%'.$query->get('name').'%');
//        }
//
//        switch ($query->get('password')) {
//            case 'true':
//                if ($orWhere) {
//                    $dql->orWhere($this->getTableName().'.password IS NOT NULL');
//                } else {
//                    $dql->andWhere($this->getTableName().'.password IS NOT NULL');
//                }
//                break;
//            case 'false':
//                if ($orWhere) {
//                    $dql->orWhere($this->getTableName().'.password IS NULL');
//                } else {
//                    $dql->andWhere($this->getTableName().'.password IS NULL');
//                }
//        }
//
//        if ($query->get('post')) {
//            if ($orWhere) {
//                $dql->orWhere('post.title LIKE :post');
//            } else {
//                $dql->andWhere('post.title LIKE :post');
//            }
//            $dql->leftJoin($this->getTableName().'.post', 'post');
//            $dql->setParameter(':post', '%'.$query->get('post').'%');
//        }
//
//        switch ($query->get('published')) {
//            case 'true':
//                if ($orWhere) {
//                    $dql->orWhere($this->getTableName().'.published = :published');
//                } else {
//                    $dql->andWhere($this->getTableName().'.published = :published');
//                }
//                $dql->setParameter(':published', 1);
//                break;
//            case 'false':
//                if ($orWhere) {
//                    $dql->orWhere($this->getTableName().'.published = :published');
//                } else {
//                    $dql->andWhere($this->getTableName().'.published = :published');
//                }
//                $dql->setParameter(':published', 0);
//        }
//
//        if ($query->get('section')) {
//            if ($orWhere) {
//                $dql->orWhere('section.title LIKE :section');
//            } else {
//                $dql->andWhere('section.title LIKE :section');
//            }
//            $dql->leftJoin($this->getTableName().'.sections', 'section');
//            $dql->setParameter(':section', '%'.$query->get('section').'%');
//        }
//
//        if ($query->get('slug')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.slug LIKE :slug');
//            } else {
//                $dql->andWhere($this->getTableName().'.slug LIKE :slug');
//            }
//            $dql->setParameter(':slug', '%'.$query->get('slug').'%');
//        }
//
//        if ($query->get('subtitle')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.subtitle LIKE :subtitle');
//            } else {
//                $dql->andWhere($this->getTableName().'.subtitle LIKE :subtitle');
//            }
//            $dql->setParameter(':subtitle', '%'.$query->get('subtitle').'%');
//        }
//
//        if ($query->get('summary')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.summary LIKE :summary');
//            } else {
//                $dql->andWhere($this->getTableName().'.summary LIKE :summary');
//            }
//            $dql->setParameter(':summary', '%'.$query->get('summary').'%');
//        }
//
//        if ($query->get('s_page')) {
//            if ($orWhere) {
//                $dql->orWhere('page.title LIKE :page');
//            } else {
//                $dql->andWhere('page.title LIKE :page');
//            }
//            $dql->leftJoin($this->getTableName().'.pages', 'page');
//            $dql->setParameter(':page', '%'.$query->get('s_page').'%');
//        }
//
//        if ($query->get('tag')) {
//            if ($orWhere) {
//                $dql->orWhere('tag.name LIKE :tag');
//            } else {
//                $dql->andWhere('tag.name LIKE :tag');
//            }
//            $dql->leftJoin($this->getTableName().'.tags', 'tag');
//            $dql->setParameter(':tag', '%'.$query->get('tag').'%');
//        }
//
//        if ($query->get('text')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.text LIKE :text');
//            } else {
//                $dql->andWhere($this->getTableName().'.text LIKE :text');
//            }
//            $dql->setParameter(':text', '%'.$query->get('text').'%');
//        }
//
//        if ($query->get('title')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.title LIKE :title');
//            } else {
//                $dql->andWhere($this->getTableName().'.title LIKE :title');
//            }
//            $dql->setParameter(':title', '%'.$query->get('title').'%');
//        }
//
//        if ($query->get('type')) {
//            if ($orWhere) {
//                $dql->orWhere($this->getTableName().'.type LIKE :type');
//            } else {
//                $dql->andWhere($this->getTableName().'.type LIKE :type');
//            }
//            $dql->setParameter(':type', '%'.$query->get('type').'%');
//        }
//
//        if ($query->get('user')) {
//            if ($orWhere) {
//                $dql->orWhere('user.username LIKE :user');
//            } else {
//                $dql->andWhere('user.username LIKE :user');
//            }
//            $dql->leftJoin($this->getTableName().'.user', 'user');
//            $dql->setParameter(':user', '%'.$query->get('user').'%');
//        }
//
//        if ($query->get('username')) {
//            if ($orWhere) {
//                $dql->orWhere('user.username LIKE :username');
//            } else {
//                $dql->andWhere('user.username LIKE :username');
//            }
//            $dql->setParameter(':username', '%'.$query->get('username').'%');
//        }

        return $dql;
    }
}
