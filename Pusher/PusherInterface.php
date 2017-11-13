<?php

namespace Openpp\PushNotificationBundle\Pusher;

interface PusherInterface
{
    /**
     * Sends the push notification.
     *
     * @param string|\Openpp\PushNotificationBundle\Model\ApplicationInterface $application   Application name
     * @param string                                                           $tagExpression Tag expression
     * @param string                                                           $message       Notificaton message
     * @param array                                                            $options       Notificaton options
     */
    public function push($application, $tagExpression, $message, array $options);

    /**
     * Sends the push notification to the given diveces.
     *
     * @param string|\Openpp\PushNotificationBundle\Model\ApplicationInterface $application Application name
     * @param int[]|\Openpp\PushNotificationBundle\Model\DeviceInterface[]     $devices     Devices
     * @param string                                                           $message     Notificaton  message
     * @param array                                                            $options     Notificaton  options
     */
    public function pushToDevice($application, $devices, $message, array $options);

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
     * @param string $applicationName  Application name
     * @param string $deviceIdentifier Device identifier
     * @param array  $tags             Tags
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags);

    /**
     * @param string $applicationName  Application name
     * @param string $deviceIdentifier Device identifier
     * @param array  $tags             Tags
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags);

    /**
     * @param string $applicationName Application name
     * @param int    $type            Device type
     * @param string $registrationId  Registration ID related to the push service
     * @param string $eTag            ETag related to the push service
     *
     * @throws \Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag);
}
