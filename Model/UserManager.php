<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class UserManager implements UserManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUid(ApplicationInterface $application, $uid)
    {
        return $this->findUserBy(['application' => $application, 'uid' => $uid]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasUserWithTag(ApplicationInterface $application, $target, $type = null)
    {
        //TODO
        return true;
    }
}
