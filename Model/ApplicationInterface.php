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
     * Returns the application's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the application's name.
     *
     * @param string $name
     */
    public function setName($name);

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
     * @param DeviceInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * Removes a user.
     *
     * @param DeviceInterface $user
     */
    public function removeUser(UserInterface $user);
}