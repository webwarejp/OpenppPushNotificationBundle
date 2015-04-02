<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Openpp\PushNotificationBundle\Model\UserManager as BaseManager;

class UserManager extends BaseManager
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
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}