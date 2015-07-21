<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class Condition implements ConditionInterface
{
    const INTERVAL_TYPE_HOURLY  = 1;
    const INTERVAL_TYPE_DAILY   = 2;
    const INTERVAL_TYPE_WEEKLY  = 3;
    const INTERVAL_TYPE_MONTHLY = 4;

    const TIME_TYPE_NONE = 0;
    const TIME_TYPE_SPECIFIC = 1;
    const TIME_TYPE_PERIODIC = 2;

    protected static $intervalTypeChoices = array(
        self::INTERVAL_TYPE_HOURLY => 'hourly',
        self::INTERVAL_TYPE_DAILY  => 'daily',
        self::INTERVAL_TYPE_WEEKLY => 'weekly',
        self::INTERVAL_TYPE_MONTHLY => 'monthly'
    );

    protected static $timeTypeChoices = array(
        self::TIME_TYPE_NONE     => 'None',
        self::TIME_TYPE_SPECIFIC => 'Specific Dates',
        self::TIME_TYPE_PERIODIC => 'Periodic'
    );

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $enable;

    /**
     * @var \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected $application;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $tagExpression;

    /**
     * @var integer
     */
    protected $timeType;

    /**
     * @var \Datetime[]
     */
    protected $specificDates;

    /**
     * @var \Datetime
     */
    protected $startDate;

    /**
     * @var \Datetime
     */
    protected $endDate;

    /**
     * @var integer
     */
    protected $intervalType;

    /**
     * @var integer
     */
    protected $intervalTime;

    /**
     * @var \CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface
     */
    protected $area;

    /**
     * @var \Datetime
     */
    protected $createdAt;

    /**
     * @var \Datetime
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setEnable(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * {@inheritdoc}
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagExpression()
    {
        return $this->tagExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function setTagExpression($tagExpression)
    {
        $this->tagExpression = $tagExpression;
    }

    public function getTimeType()
    {
        return $this->timeType;
    }

    public function setTimeType($timeType)
    {
        $this->timeType = $timeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate ? new \DateTime($startDate->format('Y-m-d H:i')) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate ? new \DateTime($endDate->format('Y-m-d H:i')) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntervalType()
    {
        return $this->intervalType;
    }

    /**
     * {@inheritdoc}
     */
    public function setIntervalType($intervalType)
    {
        $this->intervalType = $intervalType;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntervalTime()
    {
        return $this->intervalTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setIntervalTime($intervalTime)
    {
        $this->intervalTime = $intervalTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecificDates()
    {
        return $this->specificDates;
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecificDates(array $specificDates)
    {
        $dates = array();
        foreach ($specificDates as $date) {
            $dates[] = new \DateTime($date->format('Y-m-d H:i'));
        }

        $this->specificDates = $dates;
    }

    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * {@inheritdoc}
     */
    public function setArea(\CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface $area)
    {
        $this->area = $area;
    }

    /**
     * Returns the creation date
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Returns the last update date
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last update date
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Returns the interval type choices.
     *
     * @return array
     */
    public static function getTimeTypeChoices()
    {
        return self::$timeTypeChoices;
    }

    /**
     * Returns the interval type choices.
     *
     * @return array
     */
    public static function getIntervalTypeChoices()
    {
        return self::$intervalTypeChoices;
    }

    /**
     * Returns the DateInterval instance according to this interval type.
     *
     * @return \DateInterval
     */
    public function getDateInterval()
    {
        switch ($this->getIntervalType()) {
            case self::INTERVAL_TYPE_HOURLY:
                $interval = new \DateInterval('PT1H');
                break;
            case self::INTERVAL_TYPE_DAILY:
                $interval = new \DateInterval('P1D');
                break;
            case self::INTERVAL_TYPE_WEEKLY:
                $interval = new \DateInterval('P1W');
                break;
            case self::INTERVAL_TYPE_MONTHLY:
                $interval = new \DateInterval('P1M');
                break;
            default:
                $interval = null;
                break;
        }

        return $interval;
    }

    /**
     * Returns whether the periodic setting is valid.
     *
     * @return boolean
     */
    public function isPeriodicSettingValid()
    {
        if ($this->getTimeType() === self::TIME_TYPE_PERIODIC) {
            if (!$this->getStartDate() && !$this->getEndDate() && !$this->getIntervalType()) {
                return true;
            }
            if (!$this->getStartDate()) {
                return false;
            }
            if (!$this->getIntervalType()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether the endDate is valid.
     *
     * @return boolean
     */
    public function isEndDateValid()
    {
        if ($this->getTimeType() === self::TIME_TYPE_PERIODIC) {
            if ($this->getStartDate() && $this->getEndDate() && $this->getStartDate() > $this->getEndDate()) {
                return false;
            }
        }

        return true;
    }
}