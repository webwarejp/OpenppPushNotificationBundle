<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\ApplicationManager as BaseManager;

class ApplicationManager extends BaseManager
{
    protected $objectManager;
    protected $repository;
    protected $class;

    /**
     * Constructor
     *
     * @param ManagerRegistry $managerRegistry
     * @param string $class
     */
    public function __construct(ManagerRegistry $managerRegistry, $class)
    {
        $this->objectManager = $managerRegistry->getManagerForClass($class);
        $this->repository = $this->objectManager->getRepository($class);

        $metadata = $this->objectManager->getClassMetadata($class);
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
    public function findApplicationBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
