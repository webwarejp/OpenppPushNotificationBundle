<?php

namespace Openpp\PushNotificationBundle\Listener;

use Openpp\PushNotificationBundle\Event\PostPushEvent;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;

class NotRegisteredDevicesListener
{
    /**
     * @var DeviceManagerInterface
     */
    protected $deviceManager;

    /**
     * Constructor
     *
     * @param DeviceManagerInterface     $deviceManager
     */
    public function __construct(DeviceManagerInterface $deviceManager)
    {
        $this->deviceManager = $deviceManager;
    }

    /**
     * Handle event.
     *
     * @param PostPushEvent $event
     */
    public function onPushed(PostPushEvent $event)
    {
        foreach ($event->getNotRegisteredDevices() as $device) {
            $this->deviceManager->delete($device);
        }
    }
}
