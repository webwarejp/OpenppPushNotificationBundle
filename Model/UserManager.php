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
    public function hasUserWithTag(ApplicationInterface $application, $target, $type = null)
    {
        //TODO
        return true;
    }
}
