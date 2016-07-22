<?php

namespace Openpp\PushNotificationBundle\Model;

use Openpp\MapBundle\Model\CircleInterface;
/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class Condition implements ConditionInterface
{
    protected static $intervalTypeChoices = array(
        self::INTERVAL_TYPE_HOURLY => 'hourly',
        self::INTERVAL_TYPE_DAILY  => 'daily',
        self::INTERVAL_TYPE_WEEKLY => 'weekly',
        self::INTERVAL_TYPE_MONTHLY => 'monthly'
    );

    protected static $timeTypeChoices = array(
        self::TIME_TYPE_SPECIFIC   => 'Specific Dates',
        self::TIME_TYPE_PERIODIC   => 'Periodic',
        self::TIME_TYPE_CONTINUING => 'Continuing',
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
     * @var \DateTime[]
     */
    protected $specificDates;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
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
     * @var \Openpp\MapBundle\Model\CircleInterface
     */
    protected $areaCircle;

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

    /**
     * {@inheritdoc}
     */
    public function getTimeType()
    {
        return $this->timeType;
    }

    /**
     * {@inheritdoc}
     */
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
    public function getAreaCircle()
    {
        return $this->areaCircle;
    }

    /**
     * {@inheritdoc}
     */
    public function setAreaCircle(CircleInterface $areaCircle)
    {
        $this->areaCircle = $areaCircle;
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
     * @param boolean $mapBundleEnable
     *
     * @return array
     */
    public static function getTimeTypeChoices($mapBundleEnable = false)
    {
        if (!$mapBundleEnable) {
            unset(self::$timeTypeChoices[self::TIME_TYPE_CONTINUING]);
        }

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
     * @return \DateInterval|null
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
     * Returns whether the continuing setting is valid.
     *
     * @return boolean
     */
    public function isContinuingSettingValid()
    {
        if ($this->getTimeType() === self::TIME_TYPE_CONTINUING) {
            if (!$this->getStartDate()) {
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
        if (in_array($this->getTimeType(), array(self::TIME_TYPE_PERIODIC, self::TIME_TYPE_CONTINUING))) {
            if ($this->getStartDate() && $this->getEndDate() && $this->getStartDate() > $this->getEndDate()) {
                return false;
            }
        }

        return true;
    }
}