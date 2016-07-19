<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Exception\ApplicationNotFoundException;

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
     * Constructor
     *
     * @param ApplicationManagerInterface  $applicationManager
     * @param TagManagerInterface          $tagManager
     * @param UserManagerInterface         $userManager
     * @param DeviceManagerInterface       $deviceManager
     */
    public function __construct(ApplicationManagerInterface $applicationManager, TagManagerInterface $tagManager, UserManagerInterface $userManager, DeviceManagerInterface $deviceManager)
    {
        $this->applicationManager = $applicationManager;
        $this->tagManager         = $tagManager;
        $this->userManager        = $userManager;
        $this->deviceManager      = $deviceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUser($applicationName, $uid, $tag)
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);
        if (!$application) {
            throw new ApplicationNotFoundException($applicationName . ' is not found.');
        }

        $tagObjects = $this->tagManager->getTagObjects($tag, true);

        $this->userManager->addTagToUser($application, $uid, $tagObjects);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUser($applicationName, $uid, $tag)
    {
        $application = $this->applicationManager->findApplicationByName($applicationName);
        if (!$application) {
            throw new ApplicationNotFoundException($applicationName . ' is not found.');
        }

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
     * Reterns whether there is the Android user associated with the tag expressions or not.
     *
     * @param ApplicationInterface $application
     * @param string $target
     */
    protected function hasAndroidTarget(ApplicationInterface $application, $target)
    {
        return $this->userManager->hasUserWithTag($application, $target, DeviceInterface::TYPE_ANDROID);
    }

    /**
     * Reterns whether there is the iOS user associated with the tag expressions or not.
     *
     * @param ApplicationInterface $application
     * @param string $target
     */
    protected function hasIOSTarget(ApplicationInterface $application, $target)
    {
        return $this->userManager->hasUserWithTag($application, $target, DeviceInterface::TYPE_IOS);
    }
}