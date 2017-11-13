<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Openpp\MapBundle\Form\DataTransformer\GeometryToStringTransformer;
use Openpp\MapBundle\Model\CircleInterface;
use Openpp\PushNotificationBundle\Collections\DeviceCollection;
use Openpp\PushNotificationBundle\Model\ApplicationInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\DeviceManager as BaseManager;
use Openpp\PushNotificationBundle\TagExpression\TagExpression;

class DeviceManager extends BaseManager
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
     * @var string
     */
    protected $userClass;

    /**
     * @var string
     */
    protected $tagClass;

    /**
     * @var string
     */
    protected $pointClass;

    /**
     * Initializes a new DeviceManager.
     *
     * @param ManagerRegistry $managerRegistry
     * @param string          $class
     * @param string          $userClass
     * @param string          $tagClass
     * @param string          $pointClass
     */
    public function __construct(ManagerRegistry $managerRegistry, $class, $userClass, $tagClass, $pointClass = '')
    {
        $this->objectManager = $managerRegistry->getManagerForClass($class);
        $this->repository = $this->objectManager->getRepository($class);

        $metadata = $this->objectManager->getClassMetadata($class);
        $this->class = $metadata->getName();

        $this->userClass = $userClass;
        $this->tagClass = $tagClass;
        $this->pointClass = $pointClass;
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
    public function delete(DeviceInterface $device)
    {
        $this->objectManager->remove($device);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function save(DeviceInterface $device, $andFlush = true)
    {
        $this->objectManager->persist($device);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findDeviceBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findDevicesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * {@inheritdoc}
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

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countActiveDevices(ApplicationInterface $application)
    {
        $qb = $this->repository->createQueryBuilder('d');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb
            ->select('COUNT(d.id)')
            ->where($qb->expr()->eq('d.application', ':application'))
            ->andWhere($qb->expr()->isNull('d.unregisteredAt'))
            ->setParameter('application', $application)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findDevicesByTagExpression(ApplicationInterface $application, $tagExpression, array $devices = [])
    {
        if (!$tagExpression) {
            return $this->findActiveDevices($application);
        }

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($this->getClass(), 'd');
        $rsm->addFieldResult('d', 'device_id', 'id');
        $rsm->addFieldResult('d', 'device_type', 'type');
        $rsm->addFieldResult('d', 'device_token', 'token');
        $rsm->addFieldResult('d', 'device_public_key', 'publicKey');
        $rsm->addFieldResult('d', 'device_auth_token', 'authToken');
        $rsm->addJoinedEntityResult($this->userClass, 'u', 'd', 'user');
        $rsm->addFieldResult('u', 'user_id', 'id');
        $rsm->addFieldResult('u', 'user_uid', 'uid');

        $sql = <<<'SQL'
WITH _device_user_tag AS ( 
  SELECT 
    d.id AS device_id,
    d.type AS device_type,
    d.token AS device_token,
    d.public_key AS device_public_key,
    d.auth_token AS device_auth_token,
    u.id AS user_id,
    u.uid AS user_uid,
    ARRAY(
      SELECT 
        t.name
      FROM
        %s t JOIN %s ut ON ut.tag_id = t.id 
      WHERE
        u.id = ut.user_id
    ) AS tags 
  FROM
    %s d JOIN %s u ON d.user_id = u.id
  WHERE
    u.application_id = ?
    AND d.unregistered_at IS NULL
    %s
)
SELECT 
 * 
FROM
  _device_user_tag dut 
WHERE 
  %s
SQL;

        $tagTableName = $this->objectManager->getClassMetadata($this->tagClass)->getTableName();
        $userTagTableName = 'push__user_tag'; // TODO
        $deviceTableName = $this->objectManager->getClassMetadata($this->class)->getTableName();
        $userTableName = $this->objectManager->getClassMetadata($this->userClass)->getTableName();

        $te = new TagExpression($tagExpression);
        $whereClause = $te->toNativeSQLWhereClause();
        $whereClause = str_replace('%s', 'dut.tags', $whereClause);

        $deviceWhereClause = '';
        if (!empty($devices)) {
            $deviceWhereClause = 'AND d.id in (?)';
        }
        $sql = sprintf($sql, $tagTableName, $userTagTableName, $deviceTableName, $userTableName, $deviceWhereClause, $whereClause);
        $query = $this->objectManager->createNativeQuery($sql, $rsm);

        $params = [];
        $params[] = $application->getId();
        if (!empty($devices)) {
            $params[] = $devices;
        }

        $key = 0;
        foreach ($params as $param) {
            $query->setParameter(++$key, $param);
        }

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
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

        $sql = <<<'SQL'
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
    AND d.unregistered_at IS NULL
    AND ST_DWithin(p.point::geography, ST_GeographyFromText(?), ?)
SQL;

        $deviceTableName = $this->objectManager->getClassMetadata($this->class)->getTableName();
        $pointTableName = $this->objectManager->getClassMetadata($this->pointClass)->getTableName();
        $userTableName = $this->objectManager->getClassMetadata($this->userClass)->getTableName();

        $sql = sprintf($sql, $deviceTableName, $pointTableName, $userTableName);
        $query = $this->objectManager->createNativeQuery($sql, $rsm);

        $params = [];
        $params[] = $application->getId();
        $params[] = $transformer->transform($circle->getCenter());
        $params[] = $circle->getRadius();

        $key = 0;
        foreach ($params as $param) {
            $query->setParameter(++$key, $param);
        }

        $result = $query->getResult();

        if (!$result || !$tagExpression) {
            return $result;
        }

        $devices = new DeviceCollection($result);

        return $this->findDevicesByTagExpression($application, $tagExpression, $devices->toIdArray()->toArray());
    }
}
