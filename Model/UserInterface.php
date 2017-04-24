<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * UserInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface UserInterface
{
    /**
     * Returns the application
     *
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * Sets the application
     *
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * Returns the user id
     *
     * @return string
     */
    public function getUid();

    /**
     * Sets the user id
     *
     * @param string $uid
     */
    public function setUid($uid);

    /**
     * Returns the devices
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDevices();

    /**
     * Adds the device
     *
     * @param DeviceInterface $device
     */
    public function addDevice(DeviceInterface $device);

    /**
     * Removes the device
     *
     * @param DeviceInterface $device
     */
    public function removeDevice(DeviceInterface $device);

    /**
     * Returns the badge
     *
     * @return integer
     */
    public function getBadge();

    /**
     * Sets the badge
     *
     * @param integer $badge
     */
    public function setBadge($badge);

    /**
     * Returns the tags
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags();

    /**
     * Adds a tag.
     *
     * @param TagInterface $tag
     */
    public function addTag(TagInterface $tag);

    /**
     * Removes a tag.
     *
     * @param TagInterface $tag
     */
    public function removeTag(TagInterface $tag);

    /**
     * Gets a device by its identifier.
     *
     * @param string $deviceIdentifier
     *
     * @return DeviceInterface
     */
    public function getDeviceByIdentifier($deviceIdentifier);

    /**
     * Gets a device by its token.
     *
     * @param string $token
     *
     * @return DeviceInterface
     */
    public function getDeviceByToken($token);

    /**
     * Gets a uid_ tag.
     *
     * @return string
     */
    public function getUidTag();

    /**
     * Gets all tag name that user has.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTagNames();
}
