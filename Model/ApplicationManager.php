<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class ApplicationManager implements ApplicationManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function findApplicationByPackageName($packageName)
    {
        return $this->findApplicationBy(array('packageName' => $packageName));
    }
}