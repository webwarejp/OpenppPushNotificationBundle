<?php

namespace Openpp\PushNotificationBundle\Model;

interface DeviceManagerInterface
{
    /**
     * Returns the device's fully qualified class name.
     *
     * @return string
     */
    public function getClass();

    /**
     * Returns an empty device instance
     *
     * @return DeviceInterface
     */
    public function createDevice();

    /**
     * Deletes a device.
     *
     * @param DeviceInterface $device
     *
     * @return void
     */
    public function deleteDevice(DeviceInterface $device);

    /**
     * Updates a device.
     *
     * @param DeviceInterface $device
     *
     * @return void
     */
    public function updateDevice(DeviceInterface $device);

    /**
     * Finds one device by its identifier and application.
     *
     * @param ApplicationInterface $application
     * @param string $deviceIdentifier
     *
     * @return DeviceInterface or null
     */
    public function findDeviceByIdentifier(ApplicationInterface $application, $deviceIdentifier);

    /**
     * Finds one device by the given criteria.
     *
     * @param array $criteria
     *
     * @return DeviceInterface or null
     */
    public function findDeviceBy(array $criteria);
}