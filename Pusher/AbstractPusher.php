<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Openpp\PushNotificationBundle\Event\PostPushEvent;

abstract class AbstractPusher implements PusherInterface
{
    /**
     * @var ApplicationManagerInterface
     */
    protected $applicationManager;
    /**
     * @var TagManagerInterface
     */
    protected $tagManager;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var DeviceManagerInterface
     */
    protected $deviceManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param ApplicationManagerInterface  $applicationManager
     * @param TagManagerInterface          $tagManager
     * @param UserManagerInterface         $userManager
     * @param DeviceManagerInterface       $deviceManager
     * @param EventDispatcherInterface     $dispatcher
     */
    public function __construct(
        ApplicationManagerInterface $applicationManager,
        TagManagerInterface         $tagManager,
        UserManagerInterface        $userManager,
        DeviceManagerInterface      $deviceManager,
        EventDispatcherInterface    $dispatcher
    ) {
        $this->applicationManager = $applicationManager;
        $this->tagManager         = $tagManager;
        $this->userManager        = $userManager;
        $this->deviceManager      = $deviceManager;
        $this->dispatcher         = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUser($applicationName, $uid, $tag)
    {
        $application = $this->getApplication($applicationName);

        $tagObjects = $this->tagManager->getTagObjects($tag, true);

        $this->userManager->addTagToUser($application, $uid, $tagObjects);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUser($applicationName, $uid, $tag)
    {
        $application = $this->getApplication($applicationName);

        $tagObjects = $this->tagManager->getTagObjects($tag, false);

        $this->userManager->removeTagFromUser($application, $uid, $tagObjects);
    }

    /**
     * {@inheritdoc}
     */
    public function createRegistration($applicationName, $deviceIdentifier, array $tags)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function updateRegistration($applicationName, $deviceIdentifier, array $tags)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRegistration($applicationName, $type, $registrationId, $eTag)
    {
    }

    /**
     * Returns the application.
     *
     * @param string|ApplicationInterface $application
     *
     * @throws ApplicationNotFoundException
     * @return \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected function getApplication($application)
    {
        if ($application instanceof ApplicationInterface) {
            return $application;
        }

        $name = $application;
        $application = $this->applicationManager->findApplicationByPackageName($name);
        if (empty($application)) {
            $application = $this->applicationManager->findApplicationBy(array('slug' => $name));
            if (empty($application)) {
                throw new ApplicationNotFoundException($application . ' is not found.');
            }
        }

        return $application;
    }

    /**
     * Generate the notification id.
     *
     * @return string
     */
    protected function generateNotificationId()
    {
        return sha1(uniqid());
    }

    /**
     * Dispatch the push result event.
     *
     * @param ApplicationInterface $application
     * @param string               $notificaitonId
     * @param string               $message
     * @param array                $options
     * @param \DateTime            $timestamp
     * @param mixed                $devices
     * @param mixed                $notRegisteredDevices
     */
    protected function dispatchPushResult(ApplicationInterface $application, $notificaitonId, $message, array $options, $timestamp, $devices, $notRegisteredDevices)
    {
        $event = new PostPushEvent($application, $notificaitonId, $message, $options, $timestamp, $devices, $notRegisteredDevices);
        $this->dispatcher->dispatch(PostPushEvent::EVENT_NAME, $event);
    }
}