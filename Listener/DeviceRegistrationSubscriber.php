<?php

namespace Openpp\PushNotificationBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\Device;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\TagInterface;

class DeviceRegistrationSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ArrayCollection
     */
    protected $creates;

    /**
     * @var ArrayCollection
     */
    protected $updates;

    /**
     * @var ArrayCollection
     */
    protected $deletes;

    /**
     * Initializes a new DeviceRegistrationSubscriber.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->creates = new ArrayCollection();
        $this->updates = new ArrayCollection();
        $this->deletes = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();

        $this->processInsersions($uow);
        $this->processUpdates($uow);
        $this->processDeletions($uow);
        $this->processCollectionUpdates($uow);

        $this->executeRegistration();
    }

    /**
     * @param UnitOfWork $uow
     */
    protected function processInsersions(UnitOfWork $uow)
    {
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof DeviceInterface) {
                $this->creates->add($entity);
            }
        }
    }

    /**
     * @param UnitOfWork $uow
     */
    protected function processUpdates(UnitOfWork $uow)
    {
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof DeviceInterface) {
                $original = $uow->getOriginalEntityData($entity);

                if ($original['unregisteredAt'] && !$entity->getUnregisteredAt()) {
                    $this->creates->add($entity);
                } elseif (!$original['unregisteredAt'] && $entity->getUnregisteredAt()) {
                    $this->deletes->add($entity);
                }

                if ($original['token'] != $entity->getToken() || $original['user'] != $entity->getUser()) {
                    $this->updates->add($entity);
                }
            }
        }
    }

    /**
     * @param UnitOfWork $uow
     */
    protected function processDeletions(UnitOfWork $uow)
    {
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof DeviceInterface) {
                $this->deletes->add($entity);
            }
        }
    }

    /**
     * @param UnitOfWork $uow
     */
    protected function processCollectionUpdates(UnitOfWork $uow)
    {
        $deletions = $uow->getScheduledCollectionDeletions();
        $updates = $uow->getScheduledCollectionUpdates();

        foreach (array_merge($deletions, $updates) as $col) {
            /* @var $col \Doctrine\ORM\PersistentCollection */
            if ($col->getOwner() instanceof UserInterface && $col->first() instanceof TagInterface) {
                $user = $col->getOwner();
                foreach ($user->getDevices() as $device) {
                    if (!$this->updates->contains($device)
                        && !$this->creates->contains($device)
                        && !$this->deletes->contains($device)
                    ) {
                        $this->updates->add($device);
                    }
                }
            }
        }
    }

    protected function executeRegistration()
    {
        foreach ($this->creates as $device) {
            $this->getPushServiceManager()->createRegistration(
                $device->getApplication()->getName(),
                $device->getDeviceIdentifier(),
                $this->getDeviceTags($device)
            );
        }
        foreach ($this->updates as $device) {
            $this->getPushServiceManager()->updateRegistration(
                $device->getApplication()->getName(),
                $device->getDeviceIdentifier(),
                $this->getDeviceTags($device)
            );
        }
        foreach ($this->deletes as $device) {
            //TODO: I'm not sure, but sometimes $device->getApplication() returns null.
            if (!$device->getApplication()) {
                continue;
            }
            $this->getPushServiceManager()->deleteRegistration(
                $device->getApplication()->getName(),
                $device->getType(),
                $device->getRegistrationId(),
                $device->getETag()
            );
        }
    }

    /**
     * Gets the Push Service Manager.
     *
     * @return \Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface
     */
    protected function getPushServiceManager()
    {
        return $this->container->get('openpp.push_notification.push_service_manager');
    }

    /**
     * Get the device's tags.
     *
     * @param DeviceInterface $device
     */
    protected function getDeviceTags(DeviceInterface $device)
    {
        $tags = $device->getUser()->getTagNames()->toArray();

        return $this->unsetDifferentDeviceTag($device->getType(), $tags);
    }

    /**
     * Removes the different device tags from tag array.
     *
     * @param int   $type
     * @param array $tags
     *
     * @return array
     */
    private function unsetDifferentDeviceTag($type, array $tags)
    {
        foreach (array_diff(array_keys(Device::getTypeChoices()), [Device::getTypeName($type)]) as $typeName) {
            if ($key = false !== array_search($typeName, $tags)) {
                unset($tags[$key]);
            }
        }

        return $tags;
    }
}
