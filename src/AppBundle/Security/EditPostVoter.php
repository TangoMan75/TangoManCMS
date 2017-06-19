<?php

namespace AppBundle\Security\Voter;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EditPostVoter extends Voter
{
    const CREATE = 'create';
    const READ   = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * EditPostVoter constructor.
     *
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
        // you only want to vote if the attribute and subject are what you expect
        if (!in_array($attribute, array(self::CREATE, self::READ, self::UPDATE, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Post) {
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
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // ROLE_SUPER_ADMIN can do anything
        if ($this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return true;
        }

        foreach ($token->getRoles() as $role) {
            if (in_array($role->getRole(), ['ROLE_MODERATOR', 'ROLE_ADMIN'])) {
                return true;
            }
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in
            return false;
        }

        $post = $subject;
        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($post, $user);

            case self::READ:
                return $this->canRead($post, $user);

            case self::UPDATE:
                return $this->canUpdate($post, $user);

            case self::DELETE:
                return $this->canDelete($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return bool
     */
    private function canRead(Post $post, User $user)
    {
        // if they can edit, they can view
        if ($this->canUpdate($post, $user)) {
            return true;
        }

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return !$post->isPrivate();
    }

    /**
     * @param Post $post
     * @param User $user
     *
     * @return bool
     */
    private function canUpdate(Post $post, User $user)
    {
        return $user === $post->getUser();
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
    private function canDelete(Post $post, User $user)
    {
        return $user === $post->getUser();
    }
}