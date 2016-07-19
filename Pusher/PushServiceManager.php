<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Sonata\NotificationBundle\Backend\BackendInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;


class PushServiceManager implements PushServiceManagerInterface
{
    const TYPE_NAME = 'openpp.push_notification.push';

    protected $backend;
    protected $tagManager;
    protected $defaultPusher;
    protected $fallbackPusher;

    /**
     * Constructor
     *
     * @param TagManagerInterface $tagManager
     * @param PusherInterface $defaultPusher
     * @param PusherInterface $fallbackPusher
     */
    public function __construct(
        BackendInterface    $backend,
        TagManagerInterface $tagManager,
        PusherInterface     $defaultPusher,
        PusherInterface     $fallbackPusher = null
    ) {
        $this->backend        = $backend;
        $this->tagManager     = $tagManager;
        $this->defaultPusher  = $defaultPusher;
        $this->fallbackPusher = $fallbackPusher;
    }

    /**
     * {@inheritdoc}
     */
    public function push($applicationName, $target, $message, array $options = array())
    {
        if ($target != '') {
            $this->tagManager->checkTagExpression($target);
        }

        $this->backend->createAndPublish(self::TYPE_NAME, array(
            'application' => $applicationName,
            'target'      => $target,
            'message'     => $message,
            'options'     => $options,
            'operation'   => self::OPERATION_PUSH,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function pushExecute($applicationName, $target, $message, array $options = array())
    {
        $this->getPusher()->push($applicationName, $target, $message, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUser($applicationName, $uid, $tag)
    {
        $this->tagManager->checkTag($tag);

        if (is_array($tag)) {
            foreach ($tag as $idx => $one) {
                if ($this->tagManager->isReservedTag($one)) {
                    unset($tag[$idx]);
                }
            }

            $tag = array_values($tag);
            if (empty($tag)) {
                return;
            }
        } else {
            if ($this->tagManager->isReservedTag($tag)) {
                return;
            }
        }

        $this->backend->createAndPublish(self::TYPE_NAME, array(
            'application' => $applicationName,
            'uid'         => $uid,
            'tag'         => $tag,
            'operation'   => self::OPERATION_ADDTAGTOUSER,
        ));
    }

    /**
     * {@inheritDoc}
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
        $this->tagManager->checkTag($tag);

        $this->backend->createAndPublish(self::TYPE_NAME, array(
            'application' => $applicationName,
            'uid'         => $uid,
            'tag'         => $tag,
            'operation'   => self::OPERATION_REMOVETAGFROMUSER,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function removeTagFromUserExecute($applicationName, $uid, $tag)
    {
        $this->getPusher()->removeTagFromUser($applicationName, $uid, $tag);
    }

    /**
     * {@inheritDoc}
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags)
    {
        $this->backend->createAndPublish(self::TYPE_NAME, array(
            'application'      => $applicationName,
            'deviceIdentifier' => $deviceIdentifier,
            'tags'             => $tags,
            'operation'        => self::OPERATION_CREATE_REGISTRATION,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function createRegistrationExecute($applicationName, $deviceIdentifier, array $tags)
    {
        $this->getPusher()->createRegistration($applicationName, $deviceIdentifier, $tags);
    }

    /**
     * {@inheritDoc}
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags)
    {
        $this->backend->createAndPublish(self::TYPE_NAME, array(
            'application'      => $applicationName,
            'deviceIdentifier' => $deviceIdentifier,
            'tags'             => $tags,
            'operation'   => self::OPERATION_UPDATE_REGISTRATION,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function updateRegistrationExecute($applicationName, $deviceIdentifier, array $tags)
    {
        $this->getPusher()->updateRegistration($applicationName, $deviceIdentifier, $tags);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag)
    {
        $this->backend->createAndPublish(self::TYPE_NAME, array(
            'application'    => $applicationName,
            'type'           => $type,
            'registrationId' => $registrationId,
            'eTag'           => $eTag,
            'operation'      => self::OPERATION_DELETE_REGISTRATION,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function deleteRegistrationExecute($applicationName, $type, $registrationId, $eTag)
    {
        $this->getPusher()->deleteRegistration($applicationName, $type, $registrationId, $eTag);
    }

    /**
     * {@inheritDoc}
     */
    public function getPusher()
    {
        return $this->defaultPusher;
    }

    /**
     * {@inheritDoc}
     */
    public function getFallBackPusher()
    {
        return $this->fallbackPusher;
    }
}