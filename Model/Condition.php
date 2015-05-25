<?php

namespace Openpp\PushNotificationBundle\Model;

/**
 * 
 * @author shiroko@webware.co.jp
 *
 */
class Condition implements ConditionInterface
{
    /**
     * @var string
     */
    protected $name;

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
     * @var \CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface
     */
    protected $area;

    /**
     * @var \Datetime
     */
    protected $startDate;

    /**
     * @var \Datetime
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $interval;

    /**
     * @var \Datetime[]
     */
    protected $specificDates;

    /**
     * @var boolean
     */
    protected $enable;

    /**
     * @var \Datetime
     */
    protected $createdAt;

    /**
     * @var \Datetime
     */
    protected $updatedAt;

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
        $this->startDate = $startDate;
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
        $this->endDate = $endDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * {@inheritdoc}
     */
    public function setInerval($interval)
    {
        $this->interval = $interval;
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
}