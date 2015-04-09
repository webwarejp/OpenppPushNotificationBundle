<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class UserManager implements UserManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $user = new $class;

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserByUid(ApplicationInterface $application, $uid)
    {
        return $this->findUserBy(array('application' => $application, 'uid' => $uid));
    }

    /**
     * {@inheritDoc}
     */
    public function addTagToUser(ApplicationInterface $application, $uid, $tags)
    {
        $user = $this->findUserByUid($application, $uid);

        if (!$user) {
            $user = $this->create();
            $user->setApplication($application);
            $user->setUid($uid);
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $tag) {
            $user->addTag($tag);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeTagFromUser(ApplicationInterface $application, $uid, $tags)
    {
        $user = $this->findUserByUid($application, $uid);

        if (!$user) {
            return;
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $tag) {
            $user->removeTag($tag);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasUserWithTag(ApplicationInterface $application, $target, $type = null)
    {
        //TODO
        return true;
    }
}
