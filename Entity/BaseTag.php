<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\Tag as ModelTag;

class BaseTag extends ModelTag
{
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }
}
