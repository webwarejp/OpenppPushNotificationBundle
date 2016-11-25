<?php

namespace Openpp\PushNotificationBundle\Model;

use Openpp\MapBundle\Model\CircleInterface;

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
    public function save(DeviceInterface $device, $andFlush = true);

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
    public function findDeviceByToken(ApplicationInterface $application, $token);

    /**
     * Finds one device by the given criteria.
     *
     * @param array $criteria
     *
     * @return DeviceInterface or null
     */
    public function findDeviceBy(array $criteria);

    /**
     * Finds multiple devices by the given criteria.
     *
     * @param array $criteria
     *
     * @return array
     */
    public function findDevicesBy(array $criteria);

    /**
     * Finds all active devices for the application.
     *
     * @param ApplicationInterface $application
     *
     * @return array
     */
    public function findActiveDevices(ApplicationInterface $application);

    /**
     * Counts active devices for the application.
     *
     * @param ApplicationInterface $application
     *
     * @return array
     */
    public function countActiveDevices(ApplicationInterface $application);

    /**
     * Finds the devices which match the given tag expression.
     *
     * @param ApplicationInterface $application
     * @param string $tagExpression
     * @param array  $devices
     *
     * @return array
     */
    public function findDevicesByTagExpression(ApplicationInterface $application, $tagExpression, array $devices = array());

    /**
     * Finds the devices in specified circle area and match the given tag expression.
     *
     * @param ApplicationInterface $application
     * @param string $tagExpression
     * @param CircleInterface $circle
     */
    public function findDevicesInAreaCircleWithTag(ApplicationInterface $application, $tagExpression, CircleInterface $circle);
}
