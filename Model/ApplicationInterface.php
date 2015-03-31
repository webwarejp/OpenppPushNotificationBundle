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
     * Returns the users.
     *
     * @return ArrayCollection
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