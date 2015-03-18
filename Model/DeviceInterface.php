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
     * Returns the device identifier
     *
     * @return string
     */
    public function getDeviceIdentifier();

    /**
     * Sets the device identifier
     *
     * @param string $deviceIdentifier
     */
    public function setDeviceIdentifier($deviceIdentifier);

    /**
     * Returns the type (Android or iOS)
     *
     * @return integer
     */
    public function getType();

    /**
     * Sets the type (Android or iOS)
     *
     * @param integer $type
     */
    public function setType($type);

    /**
     * Returns the GCM Registered ID or APNS Device Token
     *
     * @return string
     */
    public function getToken();

    /**
     * Sets the GCM Registered ID or APNS Device Token
     *
     * @param string $token
     */
    public function setToken($token);

    /**
     * Returns the application
     *
     * @return \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    public function getApplication();

    /**
     * Sets the application
     *
     * @param \Openpp\PushNotificationBundle\Model\ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * Returns the user
     *
     * @return \Openpp\PushNotificationBundle\Model\UserInterface
     */
    public function getUser();

    /**
     * Sets the user
     *
     * @param \Openpp\PushNotificationBundle\Model\UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * Returns the registration date
     *
     * @return \Datetime
     */
    public function getRegisteredAt();

    /**
     * Sets the registration date
     *
     * @param \DateTime $registeredAt
     */
    public function setRegisteredAt(\DateTime $registeredAt);

    /**
     * Returns the unregistration date
     *
     * @return \Datetime
     */
    public function getUnregisteredAt();

    /**
     * Sets the unregistration date
     *
     * @param \DateTime $unregisteredAt
     */
    public function setUnregisteredAt(\DateTime $unregisteredAt);
}