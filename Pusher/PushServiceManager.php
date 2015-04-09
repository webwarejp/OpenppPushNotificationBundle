<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Symfony\Component\DependencyInjection\ContainerAware;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;

class PushServiceManager extends ContainerAware implements PushServiceManagerInterface
{
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
    public function __construct(TagManagerInterface $tagManager, PusherInterface $defaultPusher, PusherInterface $fallbackPusher = null)
    {
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

        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push', array(
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

        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push', array(
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

        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push', array(
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
        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push', array(
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
        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push', array(
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
        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push', array(
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