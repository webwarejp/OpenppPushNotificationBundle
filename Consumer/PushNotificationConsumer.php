<?php

namespace Openpp\PushNotificationBundle\Consumer;

use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;

class PushNotificationConsumer implements ConsumerInterface
{
    /**
     * @var PushServiceManagerInterface
     */
    protected $pushServiceManager;

    /**
     * Constructor
     *
     * @param PusherInterface $pusher
     */
    public function __construct(PushServiceManagerInterface $pushServiceManager)
    {
        $this->pushServiceManager = $pushServiceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $application = $event->getMessage()->getValue('application');
        $operation   = $event->getMessage()->getValue('operation');

        switch ($operation) {
            case PushServiceManagerInterface::OPERATION_PUSH:
                $target      = $event->getMessage()->getValue('target');
                $message     = $event->getMessage()->getValue('message');
                $options     = $event->getMessage()->getValue('options');
                $this->pushServiceManager->sendNotification($application, $target, $message, $options);
                break;

            case PushServiceManagerInterface::OPERATION_ADDTAGSTOUSER:
                $message     = $event->getMessage()->getValue('uid');
                $options     = $event->getMessage()->getValue('tag');
                $this->pushServiceManager->addTagToUserExecute($application, $uid, $tag);
                break;

            case PushServiceManagerInterface::OPERATION_REMOVETAGSFROMUSER:
                $message     = $event->getMessage()->getValue('uid');
                $options     = $event->getMessage()->getValue('tag');
                $this->pushServiceManager->removeTagFromUserExecute($application, $uid, $tag);
                break;

            default:
                break;
        }
    }
}