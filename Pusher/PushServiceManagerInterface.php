<?php

namespace Openpp\PushNotificationBundle\Pusher;

/**
 * PushServiceManagerInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface PushServiceManagerInterface
{
    const OPERATION_PUSH               = 'push';
    const OPERATION_ADDTAGTOUSER      = 'addTag';
    const OPERATION_REMOVETAGFROMUSER = 'removeTag';
    const OPERATION_CREATE_REGISTRATION = 'createRegistration';
    const OPERATION_UPDATE_REGISTRATION = 'updateRegistration';
    const OPERATION_DELETE_REGISTRATION = 'deleteRegistration';

    /**
     * Creates the job message to send the push notification.
     *
     * @param string $applicationName
     * @param string $target
     * @param string $message
     * @param array  $options
     */
    public function push($applicationName, $target, $message, array $options);

    /**
     * Executes to send the push notification.
     *
     * @param string $applicationName
     * @param string $target
     * @param string $message
     * @param array  $options
     */
    public function pushExecute($applicationName, $target, $message, array $options);

    /**
     * Creates the job message to add the tags to the user.
     *
     * @param string       $applicationName Application name
     * @param string       $uid             User ID
     * @param string|array $tag             Tags
     */
    public function addTagToUser($applicationName, $uid, $tag);

    /**
     * Executes to add the tags to the user.
     *
     * @param string       $applicationName Application name
     * @param string       $uid             User ID
     * @param string|array $tag             Tags
     */
    public function addTagToUserExecute($applicationName, $uid, $tag);

    /**
     * Creates the job message to remove the tags from the user.
     *
     * @param string       $applicationName Application name
     * @param string       $uid             User ID
     * @param string|array $tag             Tags
     */
    public function removeTagFromUser($applicationName, $uid, $tag);

    /**
     * Executes to remove the tags from the user.
     *
     * @param string       $applicationName Application name
     * @param string       $uid             User ID
     * @param string|array $tag             Tags
     */
    public function removeTagFromUserExecute($applicationName, $uid, $tag);

    /**
     * 
     * @param string  $applicationName  Application name
     * @param string  $deviceIdentifier Device identifier
     * @param array   $tags             Tags
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags);

    /**
     * 
     * @param string  $applicationName  Application name
     * @param string  $deviceIdentifier Device identifier
     * @param array   $tags             Tags
     */
    public function createRegistrationExecute($applicationName, $deviceIdentifier, array $tags);

    /**
     * 
     * @param string  $applicationName  Application name
     * @param string  $deviceIdentifier Device identifier
     * @param array   $tags             Tags
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags);

    /**
     * 
     * @param string  $applicationName  Application name
     * @param string  $deviceIdentifier Device identifier
     * @param array   $tags             Tags
     */
    public function updateRegistrationExecute($applicationName, $deviceIdentifier, array $tags);

    /**
     * 
     * @param string  $applicationName Application name
     * @param integer $type            Device type
     * @param string  $registrationId  Registration ID related to the push service
     * @param string  $eTag            ETag related to the push service
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag);

    /**
     * 
     * @param string  $applicationName Application name
     * @param integer $type            Device type
     * @param string  $registrationId  Registration ID related to the push service
     * @param string  $eTag            ETag related to the push service
     */
    public function deleteRegistrationExecute($applicationName, $type, $registrationId, $eTag);

    /**
     * Get a pusher.
     *
     * @return PusherInterface
     */
    public function getPusher();

    /**
     * Get a fallback pusher.
     * 
     * @return PusherInterface
     */
    public function getFallbackPusher();
}