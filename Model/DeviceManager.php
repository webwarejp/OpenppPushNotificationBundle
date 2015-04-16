<?php

namespace Openpp\PushNotificationBundle\Model;

abstract class DeviceManager implements DeviceManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $device = new $class;

        return $device;
    }

    /**
     * {@inheritDoc}
     */
    public function findDeviceByIdentifier(ApplicationInterface $application, $deviceIdentifier)
    {
        return $this->findDeviceBy(array('application' => $application, 'deviceIdentifier' => $deviceIdentifier));
    }

    /**
     * {@inheritDoc}
     */
    public function findDeviceByToken(ApplicationInterface $application, $token)
    {
        return $this->findDeviceBy(array('application' => $application, 'token' => $token));
    }
}