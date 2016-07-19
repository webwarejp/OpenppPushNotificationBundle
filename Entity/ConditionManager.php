<?php

namespace Openpp\PushNotificationBundle\Entity;

use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Openpp\PushNotificationBundle\Model\ConditionInterface;

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
     * @param \DateTime $preTime
     * @param \DateTime $now
     * @return ConditionInterface[]
     */
    public function findConditionByTime(\DateTime $preTime, \DateTime $now)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getRepository()->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->eq('c.enable', $qb->expr()->literal(true)))
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
                            'c.specificDates', $qb->expr()->literal('%'. $preTime->format('Y-m-d') . '%')
                        ),
                        $qb->expr()->like(
                            'c.specificDates', $qb->expr()->literal('%'. $now->format('Y-m-d') . '%')
                        )
                    )
                )
            )
        ;

        $qb->setParameters(array(
            'now' => $now,
            'pre' => $preTime
        ));
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param \DateTime $now
     * @param \DateInterval $margin
     * @return ConditionInterface[]
     */
    public function matchConditionByTime(\DateTime $now, \DateInterval $margin = null)
    {
        // Ignore seconds.
        $now = new \DateTime($now->format('Y-m-d H:i'));
        $preTime = clone $now;
        $preTime->sub($margin ? $margin : new \DateInterval('PT0M'));

        $conditions = $this->findConditionByTime($preTime, $now);

        $result = array();
        foreach ($conditions as $condition) {
            /* @var $condition \Openpp\PushNotificationBundle\Model\ConditionInterface */
            switch($condition->getTimeType()) {
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
}