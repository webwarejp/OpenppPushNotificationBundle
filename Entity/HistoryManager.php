<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\HistoryInterface;
use Openpp\PushNotificationBundle\Model\HistoryManagerInterface;

class HistoryManager implements HistoryManagerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Initializes a new HistoryManager.
     *
     * @param ManagerRegistry $managerRegistry
     * @param string          $class
     */
    public function __construct(ManagerRegistry $managerRegistry, $class)
    {
        $this->objectManager = $managerRegistry->getManagerForClass($class);
        $this->repository = $this->objectManager->getRepository($class);

        $metadata = $this->objectManager->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $tag = new $class();

        return $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function save(HistoryInterface $history, $andFlush = true)
    {
        $this->objectManager->persist($history);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findHistoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findHistoriesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Returns the related Object Repository.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
}
