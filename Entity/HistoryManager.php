<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\HistoryManagerInterface;
use Openpp\PushNotificationBundle\Model\HistoryInterface;

class HistoryManager implements HistoryManagerInterface
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
     * Returns the related Object Repository.
     *
     * @return ObjectRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $tag = new $class;

        return $tag;
    }

    /**
     * {@inheritDoc}
     */
    public function save(HistoryInterface $history, $andFlush = true)
    {
        $this->objectManager->persist($history);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findHistoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findHistoriesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
}
