<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Openpp\PushNotificationBundle\Event\PushResultEvent;

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

        $application = $this->applicationManager->findApplicationByPackageName($application);
        if (!$application) {
            throw new ApplicationNotFoundException($application . ' is not found.');
        }

        return $application;
    }

    /**
     * Dispatch the push result event.
     *
     * @param ApplicationInterface $application
     * @param string               $message
     * @param array                $options
     * @param \DateTime            $timestamp
     * @param mixed                $devices
     */
    protected function dispatchPushResult(ApplicationInterface $application, $message, array $options, $timestamp, $devices)
    {
        $event = new PushResultEvent($application, $message, $options, $timestamp, $devices);
        $this->dispatcher->dispatch(PushResultEvent::EVENT_NAME, $event);
    }
}