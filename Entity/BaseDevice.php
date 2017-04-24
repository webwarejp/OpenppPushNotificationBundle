<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\Device as ModelDevice;

abstract class BaseDevice extends ModelDevice
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
