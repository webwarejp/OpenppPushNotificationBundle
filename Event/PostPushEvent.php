<?php

namespace Openpp\PushNotificationBundle\Event;

use Openpp\PushNotificationBundle\Collections\DeviceCollection;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Model\Device;
use Symfony\Component\EventDispatcher\Event;

class PostPushEvent extends Event
{
    const EVENT_NAME = 'openpp.push_notification.event.post_push';

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var string
     */
    protected $notificationId;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * @var mixed
     */
    protected $devices;

    /**
     * @var mixed
     */
    protected $notRegisteredDevices;

    /**
     * Initializes a new PostPushEvent.
     *
     * @param ApplicationInterface $application
     * @param string               $notificationId
     * @param string               $message
     * @param array                $options
     * @param \DateTime            $timestamp
     * @param mixed                $devices
     * @param mixed                $notRegisteredDevices
     */
    public function __construct(ApplicationInterface $application, $notificationId, $message, array $options, \DateTime $timestamp, $devices, $notRegisteredDevices = [])
    {
        $this->application = $application;
        $this->notificationId = $notificationId;
        $this->message = $message;
        $this->options = $options;
        $this->timestamp = $timestamp;
        $this->devices = $devices;
        $this->notRegisteredDevices = $notRegisteredDevices;
    }

    /**
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @return mixed
     */
    public function getNotRegisteredDevices()
    {
        return $this->notRegisteredDevices;
    }

    /**
     * @return array
     */
    public function getCounts()
    {
        $result = [];

        if ($this->devices instanceof DeviceCollection) {
            foreach (array_values(Device::getTypeChoices()) as $type) {
                $count = $this->devices->countByType($type);
                if ($count > 0) {
                    $result[Device::getTypeName($type)] = $count;
                }
            }
        }

        return $result;
    }
}
