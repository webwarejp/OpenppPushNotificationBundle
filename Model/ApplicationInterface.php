<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * ApplicationInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface ApplicationInterface
{
    /**
     * Returns the application's name to use as the notification title.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the application's name to use as the notification title.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns the application's package name or site URL.
     *
     * @return string
     */
    public function getPackageName();

    /**
     * Sets the application's package name or site URL.
     *
     * @param string $packageName
     */
    public function setPackageName($packageName);

    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Sets the slug.
     *
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * Returns the icon for the notification of Web Push.
     *
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getIcon();

    /**
     * Sets the icon for the notification of Web Push.
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $icon
     */
    public function setIcon(\Sonata\MediaBundle\Model\MediaInterface $icon = null);

    /**
     * Returns the application's descriptrion.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the application's descritprion.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Returns the connection string for Notification Hub.
     *
     * @return string
     */
    public function getConnectionString();

    /**
     * Sets the connection string for Notification Hub.
     *
     * @param string $connectionString
     */
    public function setConnectionString($connectionString);

    /**
     * Returns the hub name of Notification Hub.
     *
     * @return string
     */
    public function getHubName();

    /**
     * Sets the hub name of Notification Hub.
     *
     * @param string $hubName
     */
    public function setHubName($hubName);

    /**
     * Returns the Apns Template for the Notification Hub.
     *
     * @return string
     */
    public function getApnsTemplate();

    /**
     * Sets the Apns Template for the Notification Hub.
     *
     * @param string $apnsTemplate
     */
    public function setApnsTemplate($apnsTemplate);

    /**
     * Returns the Apns Template for the Notification Hub.
     *
     * @return string
     */
    public function getGcmTemplate();

    /**
     * Sets the GCM Template for the Notification Hub.
     *
     * @param string $gcmTemplate
     */
    public function setGcmTemplate($gcmTemplate);

    /**
     * Returns the Apns certificate.
     *
     * @return string
     */
    public function getApnsCertificate();

    /**
     * Sets the Apns certificate.
     *
     * @param string $apnsCertificate
     */
    public function setApnsCertificate($apnsCertificate);

    /**
     * Returns the GCM api key.
     *
     * @return string
     */
    public function getGcmApiKey();

    /**
     * Sets the GCM api key.
     *
     * @param string $gcmApiKey
     */
    public function setGcmApiKey($gcmApiKey);

    /**
     * Returns the users.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getUsers();

    /**
     * Adds a user.
     *
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * Removes a user.
     *
     * @param UserInterface $user
     */
    public function removeUser(UserInterface $user);

    /**
     * Returns the devices.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDevices();

    /**
     * Adds a device.
     *
     * @param DeviceInterface $device
     */
    public function addDevice(DeviceInterface $device);

    /**
     * Removes a device.
     *
     * @param DeviceInterface $device
     */
    public function removeDevice(DeviceInterface $device);

    /**
     * Returns the count of active devices.
     *
     * @return integer
     */
    public function countActiveDevices();

    /**
     * Returns the APNS certificate file.
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getApnsCertificateFile();

    /**
     * Sets the APNS certificate file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setApnsCertificateFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file = null);
}
