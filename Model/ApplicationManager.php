<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class ApplicationManager implements ApplicationManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findApplicationByPackageName($packageName)
    {
        return $this->findApplicationBy(['packageName' => $packageName]);
    }
}
