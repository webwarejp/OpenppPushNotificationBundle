<?php

namespace Openpp\PushNotificationBundle\Pusher;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
interface PusherInterface
{
    /**
     * Sends the push notification.
     *
     * @param string $applicationName Application name
     * @param string $target          Target devices expressed by tag or tag expression
     * @param string $message         Notificaton message
     * @param array  $options         Notificaton options
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function push($applicationName, $target, $message, array $options);

    /**
     * Adds the tags to the user.
     *
     * @param string       $applicationName Application name
     * @param string       $uid             User ID
     * @param string|array $tag             Tags
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function addTagToUser($applicationName, $uid, $tag);

    /**
     * Removes the tags from the user.
     *
     * @param string       $applicationName Application name
     * @param string       $uid             User ID
     * @param string|array $tag             Tags
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function removeTagFromUser($applicationName, $uid, $tag);

    /**
     * 
     * @param string $applicationName  Application name
     * @param string $deviceIdentifier Device identifier
     * @param array  $tags             Tags
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags);

    /**
     * 
     * @param string $applicationName  Application name
     * @param string $deviceIdentifier Device identifier
     * @param array  $tags             Tags
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags);

    /**
     * 
     * @param string  $applicationName Application name
     * @param integer $type            Device type
     * @param string  $registrationId  Registration ID related to the push service
     * @param string  $eTag            ETag related to the push service
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag);
}