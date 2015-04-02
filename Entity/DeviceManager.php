<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Openpp\PushNotificationBundle\Model\DeviceManager as BaseManager;
use Openpp\PushNotificationBundle\Model\DeviceInterface;

class DeviceManager extends BaseManager
{
    protected $objectManager;
    protected $repository;
    protected $class;

    /**
     * Constructor
     *
     * @param ObjectManager $om
     * @param string $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDevice(DeviceInterface $device)
    {
        $this->objectManager->remove($device);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function updateDevice(DeviceInterface $device, $andFlush = true)
    {
        $this->objectManager->persist($device);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findDeviceBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}