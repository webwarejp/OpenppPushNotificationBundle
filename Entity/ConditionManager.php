<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class ConditionManager implements ConditionManagerInterface
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
     * Returns the related Object Repository.
     *
     * @return ObjectRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * 
     * @param \Datetime $time
     * @return multitype:
     */
    public function findConditionByTime(\Datetime $startTime, \Datetime $endTime)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getRepository()->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->andX(
                $qb->expr()->isNotNull('c.startDate'),
                $qb->expr()->lt('c.startDate', ':start'),
                $qb->expr()->orX(
                    $qb->expr()->isNull('c.endDate'),
                    $qb->expr()->lte('c.endDate', ':end')
                )
            ))
            ->orWhere($qb->expr()->orX(
                $qb->expr()->like(
                    'c.specificDates', $qb->expr()->literal('%'. $startTime->format('Y-m-d') . '%')
                ),
                $qb->expr()->like(
                    'c.specificDates', $qb->expr()->literal('%'. $endTime->format('Y-m-d') . '%')
                )
            ))
        ;

        $qb->setParameters(array(
            'start' => $startTime,
            'end'   => $endTime
        ));
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param \DateTime $now
     * @param \DateInterval $margin
     * @return multitype:unknown
     */
    public function matchConditionByTime(\DateTime $now, \DateInterval $margin = null)
    {
        // Ignore seconds.
        $endTime = new \DateTime($now->format('Y-m-d H:i'));
        $startTime = clone $endTime;
        $startTime->sub($margin ? $margin : new \DateInterval('PT0M'));

        $conditions = $this->findConditionByTime($startTime, $endTime);

        $result = array();
        foreach ($conditions as $condition) {
            if ($condition->getSpecificDates()) {
                foreach ($condition->getSpecificDates() as $date) {
                    if ($startTime < $date && $date <= $endTime) {
                        $result[] = $condition;
                        break;
                    }
                }
            } else if ($condition->getStartDate()) {
                $t = clone $condition->getStartDate();
                $interval = $condition->getDateInterval();

                while (true) {
                    if ($endTime < $t || ($condition->getEndDate() && $condition->getEndDate() < $t)) {
                        break;
                    }
                    if ($startTime < $t && $t <= $endTime) {
                        $result[] = $condition;
                        break;
                    }
                    $t->add($interval);
                }
            }
        }

        return $result;
    }
}