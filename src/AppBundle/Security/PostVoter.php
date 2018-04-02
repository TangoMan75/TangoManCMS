<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Security;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{

    const CAN_CREATE_POST = 'CAN_CREATE_POST';

    const CAN_READ_POST = 'CAN_READ_POST';

    const CAN_UPDATE_POST = 'CAN_UPDATE_POST';

    const CAN_DELETE_POST = 'CAN_DELETE_POST';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        if ( ! in_array(
            $attribute,
            [
                self::CAN_CREATE_POST,
                self::CAN_READ_POST,
                self::CAN_UPDATE_POST,
                self::CAN_DELETE_POST,
            ]
        )) {
            return false;
        }

        if ( ! $subject instanceof Post) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(
        $attribute,
        $subject,
        TokenInterface $token
    ) {
        // ROLE_SUPER_ADMIN can do anything
        if ($this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return true;
        }

        //        // ROLE_SUPER_USER, ROLE_ADMIN can do anything as well
        //        foreach ($token->getRoles() as $role) {
        //            if (in_array($role->getRole(), ['ROLE_SUPER_USER', 'ROLE_ADMIN'])) {
        //                return true;
        //            }
        //        }

        //        // Permission is denied when user not logged in
        //        if (!$this->decisionManager->decide($token, ['IS_AUTHENTICATED_REMEMBERED'])) {
        //            return false;
        //        }
        $user = $token->getUser();
        if ( ! $user instanceof User) {
            return false;
        }

        $post = $subject;
        switch ($attribute) {
            case self::CAN_CREATE_POST:
                return $this->canCreate($post, $user);

            case self::CAN_READ_POST:
                return $this->canRead($post, $user);

            case self::CAN_UPDATE_POST:
                return $this->canUpdate($post, $user);

            case self::CAN_DELETE_POST:
                return $this->canDelete($post, $user);
        }

        throw new \LogicException('And error has occured!');
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return bool
     */
    private function canCreate(Post $post, User $user)
    {
        return $user === $post->getUser();
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return bool
     */
    private function canRead(Post $post, User $user)
    {
        // If user can edit, user can view
        if ($this->canUpdate($post, $user)) {
            return true;
        }

        // Permission is denied when post is not published
        if ($post->isPublished()) {
            return true;
        }

        // User owning CAN_READ_POST permission can view posts
        if (in_array(self::CAN_READ_POST, $user->getPrivilegesAsArray())) {
            return true;
        }

        return false;
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return bool
     */
    private function canUpdate(Post $post, User $user)
    {
        // Post owner has permission to edit
        if ($user === $post->getUser()) {
            return true;
        }

        // User owning CAN_UPDATE_POST permission can edit posts
        if (in_array(self::CAN_UPDATE_POST, $user->getPrivilegesAsArray())) {
            return true;
        }

        return false;
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return bool
     */
    private function canDelete(Post $post, User $user)
    {
        return $user === $post->getUser();
    }
}