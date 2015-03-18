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
    const OPERATION_ADDTAGTOUSER      = 'add_tag_to_user';
    const OPERATION_REMOVETAGFROMUSER = 'remove_tag_from_user';

    /**
     * Send the push notification.
     *
     * @param string $application
     * @param string $target
     * @param string $message
     * @param array $options
     */
    public function push($application, $target, $message, array $options);

    /**
     * Adds tags to user.
     *
     * @param string $application Application Name
     * @param string $uid         User ID
     * @param mixed  $tag         tag(s) (string|array)
     */
    public function addTagToUser($application, $uid, $tag);

    /**
     * Remove tags from user.
     *
     * @param string $application Application Name
     * @param string $uid         User ID
     * @param mixed  $tag         tag(s) (string|array)
     */
    public function removeTagFromUser($application, $uid, $tag);

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