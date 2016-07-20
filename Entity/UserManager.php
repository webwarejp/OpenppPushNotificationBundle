<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Openpp\PushNotificationBundle\Model\UserManager as BaseManager;
use Openpp\PushNotificationBundle\Model\UserInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\MapBundle\Model\CircleInterface;
use Openpp\MapBundle\Form\DataTransformer\GeometryToStringTransformer;

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
     * @param string $deviceClass
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
    public function findUserInAreaCircleWithTag(ApplicationInterface $application, $tagExpression, CircleInterface $circle)
    {
        $transformer = new GeometryToStringTransformer();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->repository->createQueryBuilder('u');
        $qb
            ->distinct()
            ->innerJoin('u.devices', 'd')
            ->innerJoin('d.location', 'l')
            ->where($qb->expr()->eq('u.application', ':application'))
            ->andWhere('l.point = ST_Intersection(l.point, ST_Buffer(:center, :radius))')
            ->setParameter('application', $application)
            ->setParameter('center', $transformer->transform($circle->getCenter()))
            ->setParameter('radius', $circle->getRadius())
        ;

        //TODO: check tags
        return $qb->getQuery()->getResult();
    }
}