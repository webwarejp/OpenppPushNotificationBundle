<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\NotificationHubsRest\NotificationHub;
use Openpp\NotificationHubsRest\Notification;

class AzurePusher extends AbstractPusher
{
    protected $hubs;

    /**
     * {@inheritdoc}
     */
    public function sendNotification(ApplicationInterface $application, $target, $message, array $options = array())
    {
        $notifications = $this->createNotifications($application, $target, $message, $options);

        foreach ($notifications as $notification) {
            if (!$target || TagManagerInterface::BROADCAST_TAG === $target) {
                $this->getHub($application)->broadcastNotification($notification);
            } else {
                $this->getHub($application)->sendNotification($notification, $target);
            }
        }
    }

    /**
     * Gets a Notification Hub for the application.
     *
     * @param ApplicationInterface $application
     *
     * @return NotificationHub
     */
    protected function getHub(ApplicationInterface $application)
    {
        if (!isset($this->hubs[$application->getHubName()])) {
            $this->hubs[$application->getHubName()] = new NotificationHub($application->getConnectionString(), $application->getHubName());
        }

        return $this->hubs[$application->getHubName()];
    }

    /**
     * Creates Notifications.
     *
     * @param ApplicationInterface $application
     * @param string $target
     * @param string $message
     * @param array $options
     *
     * @return Notification
     */
    protected function createNotifications(ApplicationInterface $application, $target, $message, array $options)
    {
        $notifications = array();

        if ($this->hasAndroidTarget($application, $target)) {
            $message = '{"data":{"msg":'.$message.'}}';
            $notifications[] = new Notification("gcm", $message);
        }

        if ($this->hasIOSTarget($application, $target)) {
            $alert = '{"aps":{"alert":'.$message.'}}';
            $notifications[] = new Notification("apple", $alert);
        }

        return $notifications;
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUserExecute($application, $uid, $tag)
    {
        parent::addTagToUserExecute($application, $uid, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUserExecute($application, $uid, $tag)
    {
        parent::removeTagFromUser($application, $uid, $tag);
    }
}