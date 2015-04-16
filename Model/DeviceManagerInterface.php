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
    public function create();

    /**
     * Deletes a device.
     *
     * @param DeviceInterface $device
     *
     * @return void
     */
    public function delete(DeviceInterface $device);

    /**
     * Saves a device.
     *
     * @param DeviceInterface $device
     * @param boolean $andFlush
     *
     * @return void
     */
    public function save(DeviceInterface $device, $andFlush);

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
     * Finds one device by its token and application.
     *
     * @param ApplicationInterface $application
     * @param string $token
     *
     * @return DeviceInterface or null
     */
    public function findDeviceByToken(ApplicationInterface $application, $deviceIdentifier);

    /**
     * Finds one device by the given criteria.
     *
     * @param array $criteria
     *
     * @return DeviceInterface or null
     */
    public function findDeviceBy(array $criteria);
}