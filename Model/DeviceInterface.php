<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * DeviceInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface DeviceInterface
{
    const TYPE_ANDROID = 0;
    const TYPE_IOS     = 1;

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
     * Returns the GCM Registered ID or APNS Device Token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Sets the GCM Registered ID or APNS Device Token.
     *
     * @param string $token
     */
    public function setToken($token);

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
     * @return \CrEOF\Spatial\PHP\Types\Geometry\Point
     */
    public function getLocation();

    /**
     * Sets the device's location.
     *
     * @param \CrEOF\Spatial\PHP\Types\Geometry\Point $location
     */
    public function setLocation(\CrEOF\Spatial\PHP\Types\Geometry\Point $location);

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
}