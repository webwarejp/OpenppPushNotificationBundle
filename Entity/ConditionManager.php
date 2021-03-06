<?php

namespace Openpp\PushNotificationBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Openpp\PushNotificationBundle\Model\ConditionInterface;
use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;

class ConditionManager implements ConditionManagerInterface
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
     * Initializes a new ConditionManager.
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
     * Returns the conditions which match the specified time and which has the periodic condition.
     *
     * @param \DateTime $preTime
     * @param \DateTime $now
     *
     * @return ConditionInterface[]
     */
    public function findConditionByTime(\DateTime $preTime, \DateTime $now)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getRepository()->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->eq('c.enable', $qb->expr()->literal(true)))
            ->andWhere($qb->expr()->in('c.timeType', ':timeTypes'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('c.startDate'),
                        $qb->expr()->lte('c.startDate', ':now'),
                        $qb->expr()->orX(
                            $qb->expr()->isNull('c.endDate'),
                            $qb->expr()->gte('c.endDate', ':pre')
                        )
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->like(
                            'c.specificDates', $qb->expr()->literal('%'.$preTime->format('Y-m-d').'%')
                        ),
                        $qb->expr()->like(
                            'c.specificDates', $qb->expr()->literal('%'.$now->format('Y-m-d').'%')
                        )
                    )
                )
            )
        ;

        $qb->setParameters([
            'now' => $now,
            'pre' => $preTime,
            'timeTypes' => [ConditionInterface::TIME_TYPE_PERIODIC, ConditionInterface::TIME_TYPE_SPECIFIC],
        ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns the conditions which reach in time to send.
     *
     * @param \DateTime     $now
     * @param \DateInterval $margin
     *
     * @return ConditionInterface[]
     */
    public function matchConditionByTime(\DateTime $now, \DateInterval $margin = null)
    {
        // Ignore seconds.
        $now = new \DateTime($now->format('Y-m-d H:i'));
        $preTime = clone $now;
        $preTime->sub($margin ? $margin : new \DateInterval('PT0M'));

        $conditions = $this->findConditionByTime($preTime, $now);

        $result = [];
        foreach ($conditions as $condition) {
            /** @var $condition \Openpp\PushNotificationBundle\Model\ConditionInterface */
            switch ($condition->getTimeType()) {
                case ConditionInterface::TIME_TYPE_SPECIFIC:
                    foreach ($condition->getSpecificDates() as $date) {
                        if ($preTime < $date && $now >= $date) {
                            $result[] = $condition;

                            break;
                        }
                    }

                    break;

                case ConditionInterface::TIME_TYPE_PERIODIC:
                    $t = clone $condition->getStartDate();
                    $interval = $condition->getDateInterval();
                    while (true) {
                        if ($t > $now) {
                            break;
                        }
                        if ($preTime < $t && $now >= $t) {
                            $result[] = $condition;

                            break;
                        }
                        $t->add($interval);
                    }

                    break;

                default:
                    break;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuingConditions()
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getRepository()->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->eq('c.enable', $qb->expr()->literal(true)))
            ->andWhere($qb->expr()->eq('c.timeType', ':timeType'))
            ->andWhere($qb->expr()->andX(
                $qb->expr()->lte('c.startDate', ':now'),
                $qb->expr()->orX(
                    $qb->expr()->isNull('c.endDate'),
                    $qb->expr()->gte('c.endDate', ':now')
                )
            ))
            ->setParameter('timeType', ConditionInterface::TIME_TYPE_CONTINUING)
            ->setParameter('now', new \DateTime())
        ;

        return $qb->getQuery()->getResult();
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
