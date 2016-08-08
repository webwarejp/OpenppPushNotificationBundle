<?php

namespace Openpp\PushNotificationBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\TagManagerInterface;
use Openpp\PushNotificationBundle\Model\Device;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\TagInterface;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
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
     * Constructor
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
        return array(
            Events::onFlush,
        );
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->processInsersions($em, $uow);
        $this->processUpdates($em, $uow);
        $this->processDeletions($em, $uow);

        $this->executeRegistration();
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork    $uow
     */
    protected function processInsersions(EntityManager $em, UnitOfWork $uow)
    {
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof DeviceInterface) {
                $user = $entity->getUser();
                // Add device type tag
                $deviceTypeTag = $this->getTagManager()->getTagObject(Device::getTypeName($entity->getType()));
                $user->addTag($deviceTypeTag);

                $em->persist($deviceTypeTag);
                $uow->computeChangeSet($em->getClassMetadata(get_class($deviceTypeTag)), $deviceTypeTag);

                $this->creates->add($entity);
            }
        }
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork    $uow
     */
    protected function processUpdates(EntityManager $em, UnitOfWork $uow)
    {
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof DeviceInterface) {
                $original = $uow->getOriginalEntityData($entity);

                if ($original['unregisterdAt'] && !$entity->getUnregisteredAt()) {
                    $this->creates->add($entity);
                } else if (!$original['unregisterdAt'] && $entity->getUnregisteredAt()) {
                    $this->deletes->add($entity);
                }

                if ($original['token'] != $entity->getToken() || $original['user'] != $entity->getUser()) {
                    $this->updates->add($entity);
                }
            }
        }
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork    $uow
     */
    protected function processDeletions(EntityManager $em, UnitOfWork $uow)
    {
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof DeviceInterface) {
                $this->deletes->add($entity);
            }
        }
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork    $uow
     */
    protected function processCollectionUpdates(EntityManager $em, UnitOfWork $uow)
    {
        $deletions = $uow->getScheduledCollectionDeletions();
        $updates   = $uow->getScheduledCollectionUpdates();

        foreach (array_merge($deletions, $updates) as $col) {
            /* @var $col \Doctrine\ORM\PersistentCollection */
            if ($col->getOwner() instanceof UserInterface && $col->first() instanceof TagInterface) {
                $user = $col->getOwner();
                foreach ($user->getDevices() as $device) {
                    if (!$this->updates->contains($device)) {
                        $this->updates->add($device);
                    }
                }
            }
        }
    }

    /**
     *
     */
    protected function executeRegistration()
    {
        foreach ($this->creates as $device) {
            $tags = $device->getUser()->getTagNames()->toArray() + $device->getUser()->getUidTag();
            $tags = $this->unsetDifferentDeviceTag($device->getType(), $tags);

            $this->getPushServiceManager()->createRegistration(
                $device->getApplication()->getName(),
                $device->getDeviceIdentifier(),
                $tags
            );
        }
        foreach ($this->updates as $device) {
            $tags = $device->getUser()->getTagNames()->toArray() + $device->getUser()->getUidTag();
            $tags = $this->unsetDifferentDeviceTag($device->getType(), $tags);

            $this->getPushServiceManager()->updateRegistration(
                $device->getApplication()->getName(),
                $device->getDeviceIdentifier(),
                $tags
            );
        }
        foreach ($this->deletes as $device) {
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
     * Gets the Tag Manager.
     *
     * @return \Openpp\PushNotificationBundle\Model\TagManagerInterface
     */
    protected function getTagManager()
    {
        return $this->container->get('openpp.push_notification.manager.tag');
    }

    /**
     * Removes the different device tags from tag array.
     *
     * @param integer $type
     * @param array $tags
     * @return array
     */
    private function unsetDifferentDeviceTag($type, array $tags)
    {
        foreach (array_diff(array_keys(Device::getTypeChoices()), array(Device::getTypeName($type))) as $typeName) {
            if ($key = array_search($typeName, $tags) !== false) {
                unset($tags[$key]);
            }
        }

        return $tags;
    }
}
