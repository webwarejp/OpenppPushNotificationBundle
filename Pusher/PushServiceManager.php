<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;

class PushServiceManager implements PushServiceManagerInterface, PusherInterface
{
    protected $tagManager;
    protected $defaultPusher;
    protected $fallbackPusher;

    /**
     * Constructor
     *
     * @param PusherInterface $tagManager
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
    public function push($application, $target, $message, array $options = array())
    {
        $this->tagManager->checkTagExpression($target);

        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push_service', array(
                'application' => $application,
                'target'      => $target,
                'message'     => $message,
                'options'     => $options,
                'operation'   => self::OPERATION_PUSH,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUser($application, $uid, $tag)
    {
        $this->tagManager->checkTag($tag);

        if (is_array($tag)) {
            foreach ($tag as $idx => $one) {
                if ($this->tagManager->isReservedTag($one)) {
                    unset($tag[$idx]);
                }
            }

            $tag = array_values($tag);
            if (!$tag) {
                return;
            }
        } else {
            if ($this->tagManager->isReservedTag($tag)) {
                return;
            }
        }

        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push_service', array(
                'application' => $application,
                'uid'         => $uid,
                'tag'         => $tag,
                'operation'   => self::OPERATION_ADDTAGTOUSER,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUser($application, $uid, $tag)
    {
        $this->tagManager->checkTag($tag);

        $backend = $this->container->get('sonata.notification.backend');
        $backend->createAndPublish('openpp.push_notification.push_service', array(
                'application' => $application,
                'uid'         => $uid,
                'tag'         => $tag,
                'operation'   => self::OPERATION_REMOVETAGFROMUSER,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function sendNotification($application, $target, $message, array $options = array())
    {
        $this->getPusher()->sendNotification($application, $target, $message, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function addTagToUserExecute($application, $uid, $tag)
    {
        $this->getPusher()->addTagToUserExecute($application, $tag, $uid);
    }

    /**
     * {@inheritDoc}
     */
    public function removeTagFromUserExecute($application, $uid, $tag)
    {
        $this->getPusher()->removeTagFromUserExecute($app, $tag, $uid);
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