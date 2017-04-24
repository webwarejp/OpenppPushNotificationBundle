<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\History as ModelHistory;

abstract class BaseHistory extends ModelHistory
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
