<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class ApplicationManager implements ApplicationManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function findApplicationByName($name)
    {
        return $this->findApplicationBy(array('name' => $name));
    }
}