<?php

namespace Openpp\PushNotificationBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PrePushEvent extends Event
{
    const EVENT_NAME = 'openpp.push_notification.event.pre_push';

    protected $applicationName;
    protected $tagExpression;
    protected $message;
    protected $options;
    protected $devices;

    /**
     * @param string $applicationName
     * @param string $tagExpression
     * @param string $message
     * @param array $options
     * @param array $devices
     */
    public function __construct($applicationName, $tagExpression, $message, array $options = array(), array $devices = array())
    {
        $this->applicationName = $applicationName;
        $this->tagExpression   = $tagExpression;
        $this->message         = $message;
        $this->options         = $options;
        $this->devices         = $devices;
    }

    /**
     * @return string
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * @param string $applicationName
     *
     * @return \Openpp\PushNotificationBundle\Event\PrePushEvent
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTagExpression()
    {
        return $this->tagExpression;
    }

    /**
     * @param string $tagExpression
     *
     * @return \Openpp\PushNotificationBundle\Event\PrePushEvent
     */
    public function setTagExpression($tagExpression)
    {
        $this->tagExpression = $tagExpression;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return \Openpp\PushNotificationBundle\Event\PrePushEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return \Openpp\PushNotificationBundle\Event\PrePushEvent
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
    /**
     * @return array
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * @param array $devices
     *
     * @return \Openpp\PushNotificationBundle\Event\PrePushEvent
     */
    public function setDevices(array $devices)
    {
        $this->devices = $devices;

        return $this;
    }
}