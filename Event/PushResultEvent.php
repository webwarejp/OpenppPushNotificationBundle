<?php

namespace Openpp\PushNotificationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Collections\DeviceCollection;
use Openpp\PushNotificationBundle\Model\Device;

class PushResultEvent extends Event
{
    const EVENT_NAME = 'openpp.push_notification.event.push_result';

    /**
     * @var ApplicationInterface
     */
    protected $application;

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
     * Constructor
     *
     * @param ApplicationInterface $application
     * @param string               $message
     * @param array                $options
     * @param \DateTime            $timestamp
     * @param mixed                $devices
     */
    public function __construct(ApplicationInterface $application, $message, array $options, \DateTime $timestamp, $devices)
    {
        $this->application = $application;
        $this->message     = $message;
        $this->options     = $options;
        $this->timestamp   = $timestamp;
        $this->devices     = $devices;
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
     * @return array
     */
    public function getCounts()
    {
        $result = array();

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