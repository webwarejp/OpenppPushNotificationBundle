<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\Application as ModelApplication;

abstract class BaseApplication extends ModelApplication
{
    public function prePersist()
    {
        if (empty($this->slug)) {
            $this->slug = sha1(uniqid());
        }

        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }
}