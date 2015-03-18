<?php

namespace Openpp\PushNotificationBundle\Pusher;

use Symfony\Component\DependencyInjection\ContainerAware;

use Openpp\PushNotificationBundle\Model\ApplicationManagerInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\UserManagerInterface;

abstract class AbstractPusher extends ContainerAware implements PusherInterface
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
     * Constructor
     *
     * @param ApplicationManagerInterface  $applicationManager
     * @param TagManagerInterface          $tagManager
     * @param UserManagerInterface         $userManager
     */
    public function __construct(ApplicationManagerInterface $applicationManager, TagManagerInterface $tagManager, UserManagerInterface $userManager)
    {
        $this->applicationManager = $applicationManager;
        $this->tagManager         = $tagManager;
        $this->userManager        = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addTagToUserExecute($application, $uid, $tag)
    {
        $applicationObject = $this->applicationManager->findApplicationByName($application);
        if (!$applicationObject) {
            throw new ApplicationNotFoundException($application . ' is not found.');
        }

        $tagObjects = $this->tagManager->getTagObjects($tags, true);

        $this->userManager->addTagToUser($applicationObject, $uid, $tagObjects);
    }

    /**
     * {@inheritdoc}
     */
    public function removeTagFromUserExecute(ApplicationInterface $application, $uid, $tag)
    {
        $applicationObject = $this->applicationManager->findApplicationByName($application);
        if (!$applicationObject) {
            throw new ApplicationNotFoundException($application . ' is not found.');
        }

        $tagObjects = $this->tagManager->getTagObjects($tags, false);

        $this->userManager->removeTagFromUser($applicationObject, $uid, $tagObjects);
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