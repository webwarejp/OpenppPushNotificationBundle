<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\TagManager as BaseManager;

class TagManager extends BaseManager
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

        parent::__construct();
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
    public function findTagBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}