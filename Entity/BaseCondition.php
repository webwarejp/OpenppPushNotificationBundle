<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\Condition as ModelCondition;

abstract class BaseCondition extends ModelCondition
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