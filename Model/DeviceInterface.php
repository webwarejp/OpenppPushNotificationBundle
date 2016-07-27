<?php

namespace Openpp\PushNotificationBundle\Model;

use Sly\NotificationPusher\Model\DeviceInterface as BaseDeviceInterface;

/**
 * DeviceInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface DeviceInterface extends BaseDeviceInterface
{
    const TYPE_ANDROID = 1;
    const TYPE_IOS     = 2;
    const TYPE_NAME_ANDROID = 'android';
    const TYPE_NAME_IOS     = 'iOS';

    /**
     * Returns the device identifier.
     *
     * @return string
     */
    public function getDeviceIdentifier();

    /**
     * Sets the device identifier.
     *
     * @param string $deviceIdentifier
     */
    public function setDeviceIdentifier($deviceIdentifier);

    /**
     * Returns the type (Android or iOS).
     *
     * @return integer
     */
    public function getType();

    /**
     * Sets the type (Android or iOS).
     *
     * @param integer $type
     */
    public function setType($type);

    /**
     * Returns the registration ID related to the push service.
     *
     * @return string
     */
    public function getRegistrationId();

    /**
     * Sets the registration ID related to the push service.
     *
     * @param string $registrationId
     */
    public function setRegistrationId($registrationId);

    /**
     * Returns the ETag related to the push service.
     *
     * @return string
     */
    public function getETag();

    /**
     * Sets the ETag related to the push service.
     *
     * @param string $eTag
     */
    public function setETag($eTag);

    /**
     * Returns the device's location.
     *
     * @return \Openpp\MapBundle\Model\PointInterface
     */
    public function getLocation();

    /**
     * Sets the device's location.
     *
     * @param \Openpp\MapBundle\Model\PointInterface
     */
    public function setLocation(\Openpp\MapBundle\Model\PointInterface $location);

    /**
     * Returns the application.
     *
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * Sets the application.
     *
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * Returns the user.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Sets the user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * Returns the registration date
     *
     * @return \DateTime
     */
    public function getRegisteredAt();

    /**
     * Sets the registration date.
     *
     * @param \DateTime $registeredAt
     */
    public function setRegisteredAt(\DateTime $registeredAt);

    /**
     * Returns the unregistration date.
     *
     * @return \DateTime
     */
    public function getUnregisteredAt();

    /**
     * Sets the unregistration date.
     *
     * @param \DateTime $unregisteredAt
     */
    public function setUnregisteredAt(\DateTime $unregisteredAt);

    /**
     * Returns the type choices.
     *
     * @return array
     */
    public static function getTypeChoices();

    /**
     * Returns the type name.
     *
     * @param integer $type
     *
     * @return string
     */
    public static function getTypeName($type);
}