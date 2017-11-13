<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\PushNotificationBundle\Consumer\PushNotificationConsumer;
use Openpp\PushNotificationBundle\Event\PrePushEvent;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\TagExpression\TagExpression;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PushServiceManager implements PushServiceManagerInterface
{
    protected $dispatcher;
    protected $backend;
    protected $tagManager;
    protected $pusher;

    /**
     * Initializes a new PushServiceManager.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param BackendInterface         $backend
     * @param TagManagerInterface      $tagManager
     * @param PusherInterface          $pusher
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        BackendInterface         $backend,
        TagManagerInterface      $tagManager,
        PusherInterface          $pusher = null
    ) {
        $this->dispatcher = $dispatcher;
        $this->backend = $backend;
        $this->tagManager = $tagManager;
        $this->pusher = $pusher;
    }

    /**
     * {@inheritdoc}
     */
    public function push($applicationName, $tagExpression, $message, array $options = [])
    {
        if (!empty($tagExpression)) {
            $te = new TagExpression($tagExpression);
            $te->validate();
        }

        list(
            $applicationName,
            $tagExpression,
            $message,
            $options) = $this->dispatchPrePushEvent($applicationName, $tagExpression, $message, $options);

        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'tagExpression' => $tagExpression,
            'message' => $message,
            'options' => $options,
            'operation' => self::OPERATION_PUSH,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function pushExecute($applicationName, $tagExpression, $message, array $options = [])
    {
        $this->getPusher()->push($applicationName, $tagExpression, $message, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function pushToDevices($applicationName, $devices, $message, array $options = [])
    {
        list(
            $applicationName,
            ,
            $message,
            $options,
            $devices
        ) = $this->dispatchPrePushEvent($applicationName, null, $message, $options, $devices);

        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'devices' => $devices,
            'message' => $message,
            'options' => $options,
            'operation' => self::OPERATION_PUSH_TO_DEVICES,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function pushToDevicesExecute($applicationName, $devices, $message, array $options = [])
    {
        $this->getPusher()->pushToDevice($applicationName, $devices, $message, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUser($applicationName, $uid, $tag)
    {
        if (!is_array($tag)) {
            $tag = [$tag];
        }

        foreach ($tag as $idx => $one) {
            TagExpression::validateSingleTag($one);

            if ($this->tagManager->isReservedTag($one)) {
                unset($tag[$idx]);
            }
        }
        if (empty($tag)) {
            return;
        }

        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'uid' => $uid,
            'tag' => $tag,
            'operation' => self::OPERATION_ADDTAGTOUSER,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUserExecute($applicationName, $uid, $tag)
    {
        $this->getPusher()->addTagToUser($applicationName, $uid, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUser($applicationName, $uid, $tag)
    {
        if (!is_array($tag)) {
            $tag = [$tag];
        }

        foreach ($tag as $idx => $one) {
            TagExpression::validateSingleTag($one);

            if ($this->tagManager->isReservedTag($one)) {
                unset($tag[$idx]);
            }
        }
        if (empty($tag)) {
            return;
        }

        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'uid' => $uid,
            'tag' => $tag,
            'operation' => self::OPERATION_REMOVETAGFROMUSER,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUserExecute($applicationName, $uid, $tag)
    {
        $this->getPusher()->removeTagFromUser($applicationName, $uid, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags)
    {
        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'deviceIdentifier' => $deviceIdentifier,
            'tags' => $tags,
            'operation' => self::OPERATION_CREATE_REGISTRATION,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createRegistrationExecute($applicationName, $deviceIdentifier, array $tags)
    {
        $this->getPusher()->createRegistration($applicationName, $deviceIdentifier, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags)
    {
        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'deviceIdentifier' => $deviceIdentifier,
            'tags' => $tags,
            'operation' => self::OPERATION_UPDATE_REGISTRATION,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRegistrationExecute($applicationName, $deviceIdentifier, array $tags)
    {
        $this->getPusher()->updateRegistration($applicationName, $deviceIdentifier, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag)
    {
        $this->backend->createAndPublish(PushNotificationConsumer::TYPE_NAME, [
            'application' => $applicationName,
            'type' => $type,
            'registrationId' => $registrationId,
            'eTag' => $eTag,
            'operation' => self::OPERATION_DELETE_REGISTRATION,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRegistrationExecute($applicationName, $type, $registrationId, $eTag)
    {
        $this->getPusher()->deleteRegistration($applicationName, $type, $registrationId, $eTag);
    }

    /**
     * {@inheritdoc}
     */
    public function getPusher()
    {
        return $this->pusher;
    }

    /**
     * @param string $applicationName
     * @param string $tagExpression
     * @param string $message
     * @param array  $options
     * @param array  $devices
     *
     * @return array
     */
    protected function dispatchPrePushEvent($applicationName, $tagExpression, $message, array $options = [], $devices = [])
    {
        $event = new PrePushEvent($applicationName, $tagExpression, $message, $options, $devices);
        $event = $this->dispatcher->dispatch(PrePushEvent::EVENT_NAME, $event);

        return [
            $event->getApplicationName(),
            $event->getTagExpression(),
            $event->getMessage(),
            $event->getOptions(),
            $event->getDevices(),
        ];
    }
}
