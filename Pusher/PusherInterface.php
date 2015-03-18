<?php

namespace Openpp\PushNotificationBundle\Pusher;

interface PusherInterface
{
    /**
     * Executes to send the push notification.
     *
     * @param string $application
     * @param string $target
     * @param string $message
     * @param array $options
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function sendNotification($application, $target, $message, array $options);

    /**
     * Executes to add tags to user.
     *
     * @param string $application Application
     * @param string $uid         User ID
     * @param mixed  $tag         tag (string|array)
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function addTagToUserExecute($application, $uid, $tag);

    /**
     * Executes to remove tags from user.
     *
     * @param string $application Application
     * @param string $uid         User ID
     * @param mixed  $tag         tag (string|array)
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function removeTagFromUserExecute($application, $uid, $tag);
}