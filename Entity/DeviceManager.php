<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\DeviceManager as BaseManager;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\MapBundle\Model\CircleInterface;
use Openpp\MapBundle\Form\DataTransformer\GeometryToStringTransformer;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\ArrayCollection;

class DeviceManager extends BaseManager
{
    protected $objectManager;
    protected $repository;
    protected $class;
    protected $userClass;
    protected $pointClass;

    /**
     * Constructor
     *
     * @param ManagerRegistry $managerRegistry
     * @param string          $class
     * @param string          $userClass
     * @param string          $pointClass
     */
    public function __construct(ManagerRegistry $managerRegistry, $class, $userClass, $pointClass = '')
    {
        $this->objectManager = $managerRegistry->getManagerForClass($class);
        $this->repository = $this->objectManager->getRepository($class);

        $metadata = $this->objectManager->getClassMetadata($class);
        $this->class = $metadata->getName();

        $this->userClass = $userClass;
        $this->pointClass = $pointClass;
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
    public function delete(DeviceInterface $device)
    {
        $this->objectManager->remove($device);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function save(DeviceInterface $device, $andFlush = true)
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

    /**
     * {@inheritDoc}
     */
    public function findDevicesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findActiveDevices(ApplicationInterface $application)
    {
        $qb = $this->repository->createQueryBuilder('d');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb
            ->where($qb->expr()->eq('d.application', ':application'))
            ->andWhere($qb->expr()->isNull('d.unregisteredAt'))
            ->setParameter('application', $application)
        ;

        $result = $qb->getQuery()->getResult();
        if ($result) {
            $result = new ArrayCollection($result);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findDevicesByTagExpression(ApplicationInterface $application, $tagExpression)
    {
        // TODO: check tags.
        return $this->findActiveDevices($application);
    }

    /**
     * {@inheritDoc}
     */
    public function findDevicesInAreaCircleWithTag(ApplicationInterface $application, $tagExpression, CircleInterface $circle)
    {
        if (!$this->pointClass) {
            throw new \RuntimeException('This function can be called only when OpenppMapBundle is enable.');
        }

        $transformer = new GeometryToStringTransformer();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($this->getClass(), 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'type', 'type');
        $rsm->addFieldResult('d', 'token', 'token');
        $rsm->addJoinedEntityResult($this->userClass, 'u', 'd', 'user');
        $rsm->addFieldResult('u', 'user_id', 'id');
        $rsm->addFieldResult('u', 'uid', 'uid');

        $sql = <<<SQL
SELECT
  d.id,
  d.type,
  d.token,
  u.id AS user_id,
  u.uid
FROM
  %s d
  INNER JOIN %s p
    ON d.location_id = p.id
  INNER JOIN %s u
    ON d.user_id = u.id
  WHERE
    d.application_id = ?
    AND ST_DWithin(p.point::geography, ST_GeographyFromText(?), ?)
SQL;

        $deviceTableName = $this->objectManager->getClassMetadata($this->class)->getTableName();
        $pointTableName  = $this->objectManager->getClassMetadata($this->pointClass)->getTableName();
        $userTableName   = $this->objectManager->getClassMetadata($this->userClass)->getTableName();

        $sql = sprintf($sql, $deviceTableName, $pointTableName, $userTableName);
        $query = $this->objectManager->createNativeQuery($sql, $rsm);

        $params = array();
        $params[] = $application->getId();
        $params[] = $transformer->transform($circle->getCenter());
        $params[] = $circle->getRadius();

        $key = 0;
        foreach ($params as $param) {
            $query->setParameter(++$key, $param);
        }

        //TODO: check tags
        return $query->getResult();
    }
}