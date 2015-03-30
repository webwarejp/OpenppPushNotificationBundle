<?php

namespace Openpp\PushNotificationBundle\Consumer;

use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
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
                $target  = $event->getMessage()->getValue('target');
                $message = $event->getMessage()->getValue('message');
                $options = $event->getMessage()->getValue('options');
                $this->pushServiceManager->pushExecute($application, $target, $message, $options);
                break;

            case PushServiceManagerInterface::OPERATION_ADDTAGSTOUSER:
                $uid = $event->getMessage()->getValue('uid');
                $tag = $event->getMessage()->getValue('tag');
                $this->pushServiceManager->addTagToUserExecute($application, $uid, $tag);
                break;

            case PushServiceManagerInterface::OPERATION_REMOVETAGSFROMUSER:
                $uid = $event->getMessage()->getValue('uid');
                $tag = $event->getMessage()->getValue('tag');
                $this->pushServiceManager->removeTagFromUserExecute($application, $uid, $tag);
                break;

            case PushServiceManagerInterface::OPERATION_CREATE_REGISTRATION:
                $deviceIdentifier = $event->getMessage()->getValue('deviceIdentifier');
                $tags             = $event->getMessage()->getValue('tags');
                $this->pushServiceManager->createRegistrationExecute($application, $deviceIdentifier, $tags);
                break;

            case PushServiceManagerInterface::OPERATION_UPDATE_REGISTRATION:
                $deviceIdentifier = $event->getMessage()->getValue('deviceIdentifier');
                $tags             = $event->getMessage()->getValue('tags');
                $this->pushServiceManager->updateRegistrationExecute($application, $deviceIdentifier, $tags);
                break;

            case PushServiceManagerInterface::OPERATION_DELETE_REGISTRATION:
                $type           = $event->getMessage()->getValue('type');
                $registrationId = $event->getMessage()->getValue('registrationId');
                $eTag           = $event->getMessage()->getValue('eTag');
                $this->pushServiceManager->deleteRegistrationExecute($application, $type, $registrationId, $eTag);
                break;

            default:
                break;
        }
    }
}