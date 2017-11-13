<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class DeviceManager implements DeviceManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $device = new $class();

        return $device;
    }

    /**
     * {@inheritdoc}
     */
    public function findDeviceByIdentifier(ApplicationInterface $application, $deviceIdentifier)
    {
        return $this->findDeviceBy(['application' => $application, 'deviceIdentifier' => $deviceIdentifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function findDeviceByToken(ApplicationInterface $application, $token)
    {
        return $this->findDeviceBy(['application' => $application, 'token' => $token]);
    }
}
