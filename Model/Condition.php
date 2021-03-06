<?php

namespace Openpp\PushNotificationBundle\Model;

use Openpp\MapBundle\Model\CircleInterface;
use Openpp\PushNotificationBundle\TagExpression\TagExpression;

class Condition implements ConditionInterface
{
    protected static $intervalTypeChoices = [
        'condition.interval.hourly' => self::INTERVAL_TYPE_HOURLY,
        'condition.interval.daily' => self::INTERVAL_TYPE_DAILY,
        'condition.interval.weekly' => self::INTERVAL_TYPE_WEEKLY,
        'condition.interval.monthly' => self::INTERVAL_TYPE_MONTHLY,
    ];

    protected static $timeTypeChoices = [
        'condition.time.specific' => self::TIME_TYPE_SPECIFIC,
        'condition.time.periodic' => self::TIME_TYPE_PERIODIC,
        'condition.time.continuing' => self::TIME_TYPE_CONTINUING,
    ];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $enable;

    /**
     * @var \Openpp\PushNotificationBundle\Model\ApplicationInterface
     */
    protected $application;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $tagExpression;

    /**
     * @var int
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
     * @var int
     */
    protected $intervalType;

    /**
     * @var int
     */
    protected $intervalTime;

    /**
     * @var \Sonata\MediaBundle\Model\MediaInterface
     */
    protected $icon;

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
     * Constructor.
     */
    public function __construct()
    {
        $this->setEnable(true);
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
        $this->specificDates = $specificDates;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * {@inheritdoc}
     */
    public function setIcon(\Sonata\MediaBundle\Model\MediaInterface $icon = null)
    {
        $this->icon = $icon;
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
    public function setAreaCircle(CircleInterface $areaCircle = null)
    {
        $this->areaCircle = $areaCircle;
    }

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date.
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Returns the last update date.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last update date.
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Returns the interval type choices.
     *
     * @param bool $mapBundleEnable
     *
     * @return array
     */
    public static function getTimeTypeChoices($mapBundleEnable = false)
    {
        if (!$mapBundleEnable) {
            $key = array_search(self::TIME_TYPE_CONTINUING, self::$timeTypeChoices);
            unset(self::$timeTypeChoices[$key]);
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
     * Returns whether the specific setting is valid.
     *
     * @return bool
     */
    public function isSpecificSettingValid()
    {
        if (self::TIME_TYPE_SPECIFIC === $this->getTimeType()) {
            if (empty($this->specificDates)) {
                return false;
            }

            $now = new \DateTime();
            foreach ($this->specificDates as $date) {
                if ($now < $date) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Returns whether the periodic setting is valid.
     *
     * @return bool
     */
    public function isPeriodicSettingValid()
    {
        if (self::TIME_TYPE_PERIODIC === $this->getTimeType()) {
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
     * @return bool
     */
    public function isContinuingSettingValid()
    {
        if (self::TIME_TYPE_CONTINUING === $this->getTimeType()) {
            if (!$this->getStartDate()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether the endDate is valid.
     *
     * @return bool
     */
    public function isEndDateValid()
    {
        if (in_array($this->getTimeType(), [self::TIME_TYPE_PERIODIC, self::TIME_TYPE_CONTINUING])) {
            if ($this->getStartDate() && $this->getEndDate() && $this->getStartDate() > $this->getEndDate()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns whether the tagExpression is valid.
     *
     * @return bool
     */
    public function isTagExpressionValid()
    {
        if ($this->getTagExpression()) {
            $te = new TagExpression($this->getTagExpression());

            return $te->validate(false);
        }

        return true;
    }
}
