<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Openpp\PushNotificationBundle\Model\UserManager as BaseManager;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Application\Openpp\MapBundle\Entity\Circle;

class UserManager extends BaseManager
{
    protected $objectManager;
    protected $repository;
    protected $class;
    protected $deviceClass;

    /**
     * Constructor
     *
     * @param ObjectManager $om
     * @param string $class
     * @param string $deviceClass
     */
    public function __construct(ObjectManager $om, $class, $deviceClass)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
        $this->deviceClass = $deviceClass;
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
     * 
     * @param ApplicationInterface $application
     * @param string $tagExpression
     * @param Circle $circle
     */
    public function findUserInAreaCircleWithTag(ApplicationInterface $application, $tagExpression, Circle $circle)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->repository->createQueryBuilder('u');
        $qb
            ->distinct()
            ->innerJoin('u.devices', 'd')
            ->where($qb->expr()->eq('u.application', ':application'))
            ->andWhere('d.location = ST_Intersection(d.location, ST_Buffer(:center, :radius))')
            ->setParameter('application', $application)
            ->setParameter('center', $circle->getCenter())
            ->setParameter('radius', $circle->getRadius())
        ;

        return $qb->getQuery()->getResult();
    }
}