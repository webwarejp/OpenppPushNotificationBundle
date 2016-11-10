<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\UserManager as BaseManager;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;

class UserManager extends BaseManager
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
    public function save(UserInterface $user, $andFlush = true)
    {
        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function addTagToUser(ApplicationInterface $application, $uid, $tags, $andFlush = true)
    {
        $user = $this->findUserByUid($application, $uid);

        if (!$user) {
            $user = $this->create();
            $user->setApplication($application);
            $user->setUid($uid);
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $tag) {
            $user->addTag($tag);
        }

        if ($andFlush) {
            $this->objectManager->persist($user);
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeTagFromUser(ApplicationInterface $application, $uid, $tags, $andFlush = true)
    {
        $user = $this->findUserByUid($application, $uid);

        if (!$user) {
            return;
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }

        foreach ($tags as $tag) {
            $user->removeTag($tag);
        }

        if ($andFlush) {
            $this->objectManager->persist($user);
            $this->objectManager->flush();
        }
    }
}