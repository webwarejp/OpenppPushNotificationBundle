<?php

namespace Openpp\PushNotificationBundle\Model;

use Sly\NotificationPusher\Model\DeviceInterface as BaseDeviceInterface;

interface DeviceInterface extends BaseDeviceInterface
{
    const TYPE_ANDROID = 1;
    const TYPE_IOS = 2;
    const TYPE_WEB = 3; // Web Push
    const TYPE_NAME_ANDROID = 'android';
    const TYPE_NAME_IOS = 'ios';
    const TYPE_NAME_WEB = 'web';

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
     * @return int
     */
    public function getType();

    /**
     * Sets the type (Android or iOS).
     *
     * @param int $type
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
     * Returns the client public key for Web Push message encryption.
     *
     * @return string
     */
    public function getPublicKey();

    /**
     * Sets the client public key for Web Push message encryption.
     *
     * @param string $publicKey
     */
    public function setPublicKey($publicKey);

    /**
     * Returns the authentication secret for Web Push message encryption.
     *
     * @return string
     */
    public function getAuthToken();

    /**
     * Sets the authentication secret for Web Push message encryption.
     *
     * @param string $authToken
     */
    public function setAuthtoken($authToken);

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
     * Returns the registration date.
     *
     * @return \DateTime
     */
    public function getRegisteredAt();

    /**
     * Sets the registration date.
     *
     * @param \DateTime|null $registeredAt
     */
    public function setRegisteredAt(\DateTime $registeredAt = null);

    /**
     * Returns the unregistration date.
     *
     * @return \DateTime
     */
    public function getUnregisteredAt();

    /**
     * Sets the unregistration date.
     *
     * @param \DateTime|null $unregisteredAt
     */
    public function setUnregisteredAt(\DateTime $unregisteredAt = null);

    /**
     * Set the last delivered notification ID.
     *
     * @param string $lastDeliveredNotificationId
     */
    public function setLastDeliveredNotificationId($lastDeliveredNotificationId);

    /**
     * Get the last delivered notification ID.
     *
     * @return string
     */
    public function getLastDeliveredNotificationId();

    /**
     * Set the user agent.
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent);

    /**
     * Get the user agent.
     *
     * @return string
     */
    public function getUserAgent();

    /**
     * Returns the type choices.
     *
     * @return array
     */
    public static function getTypeChoices();

    /**
     * Returns the type name.
     *
     * @param int $type
     *
     * @return string
     */
    public static function getTypeName($type);
}
