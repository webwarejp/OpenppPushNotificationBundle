<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\User as ModelUser;

abstract class BaseUser extends ModelUser
{
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }
}