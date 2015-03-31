<?php

namespace Openpp\PushNotificationBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class TargetEventSubscriber implements EventSubscriber
{
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::preUpdate,
            Events::preRemove,
        );
    }

    /**
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof DeviceInterface) {
            $application = $entity->getApplication();
            $user = $entity->getUser();
            $uidTag = TagManagerInterface::UID_TAG_PREFIX . $user->getUid();
            $tags = $this->toTagNameArray($user->getTags());
            $tags = empty($tags) ? array($uidTag) : $tags + array($uidTag);

            $this->getPushServiceManager()->createRegistration($application->getName(), $entity->getDeviceIdentifier(), $tags);
        }
    }

    /**
     * 
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof UserInterface) {
            if (!empty($entity->getDevices()) && $args->hasChangedField('tags')) {
                $application = $entity->getApplication();
                $uidTag = TagManagerInterface::UID_TAG_PREFIX . $entity->getUid();
                $tags = $this->toTagNameArray($entity->getTags());
                $tags = empty($tags) ? array($uidTag) : $tags + array($uidTag);

                foreach ($entity->getDevices() as $device) {
                    $this->getPushServiceManager()->updateRegistration($application->getName(), $device->getDeviceIdentifier(), $tags);
                }
            }
        } else if ($entity instanceof DeviceInterface) {
            if ($args->hasChangedField('token')) {
                $uidTag = TagManagerInterface::UID_TAG_PREFIX . $entity->getUser()->getUid();
                $tags = $this->toTagNameArray($entity->getUser()->getTags());
                $tags = empty($tags) ? array($uidTag) : $tags + array($uidTag);
                $this->getPushServiceManager()->updateRegistration($entity->getApplication()->getName(), $entity->getDeviceIdentifier(), $tags);
            }
        }
    }

    /**
     * 
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof UserInterface) {
            foreach ($entity->getDevices() as $device) {
                $this->getPushServiceManager()->deleteRegistration($entity->getApplication()->getName(), $device->getType(), $device->getRegistrationId(), $device->getETag());
            }
        } else if ($entity instanceof DeviceInterface) {
            $this->getPushServiceManager()->deleteRegistration($entity->getApplication()->getName(), $entity->getType(), $entity->getRegistrationId(), $entity->getETag());
        }
    }

    /**
     * Gets the Push Service Manager.
     *
     * @return object
     */
    protected function getPushServiceManager()
    {
        return $this->container->get('openpp.push_notification.push_service_manager');
    }

    /**
     * Converts the array of tag objects to the array of tag names.
     *
     * @param array $tags
     *
     * @return array array of tag names
     */
    protected function toTagNameArray(array $tags)
    {
        $array = array();
        foreach ($tags as $tag) {
            $array[] = $tag->getName();
        }

        return $array;
    }
}