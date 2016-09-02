<?php

namespace Openpp\PushNotificationBundle\Model;

use Openpp\MapBundle\Model\CircleInterface;
/**
 * ConditionInterface
 *
 * @author shiroko@webware.co.jp
 *
 */
interface ConditionInterface
{
    const INTERVAL_TYPE_HOURLY  = 1;
    const INTERVAL_TYPE_DAILY   = 2;
    const INTERVAL_TYPE_WEEKLY  = 3;
    const INTERVAL_TYPE_MONTHLY = 4;

    const TIME_TYPE_SPECIFIC   = 1;
    const TIME_TYPE_PERIODIC   = 2;
    const TIME_TYPE_CONTINUING = 3;

    /**
     * Returns this condition's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets this condition's name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Returns whether this condition is enabled.
     *
     * @return boolean
     */
    public function isEnable();

    /**
     * Sets whether this condition is enabled.
     *
     * @param boolean $enable
    */
    public function setEnable($enable);

    /**
     * Returns the application.
     *
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * Sets the application.
     *
     * @param ApplicationInterface $application
    */
    public function setApplication(ApplicationInterface $application);

    /**
     * Returns the notification message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Sets the notification message.
     *
     * @param string $message
     */
    public function setMessage($message);

    /**
     * Returns the tag expression.
     *
     * @return string
     */
    public function getTagExpression();

    /**
     * Sets the tag expression.
     *
     * @param string $tagExpression
     */
    public function setTagExpression($tagExpression);

    /**
     * Returns the time type.
     *
     * @return integer
     */
    public function getTimeType();

    /**
     * Sets the time type.
     *
     * @param integer $timeType
     */
    public function setTimeType($timeType);

    /**
     * Returns the start date.
     *
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * Sets the start datetime.
     *
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate = null);

    /**
     * Returns the end datetime.
     *
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * Sets the end datetime.
     *
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate = null);

    /**
     * Returns the interval type.
     *
     * @return integer
     */
    public function getIntervalType();

    /**
     * Sets the interval type.
     *
     * @param integer
    */
    public function setIntervalType($intervalType);

    /**
     * Returns the interval.
     *
     * @return integer
     */
    public function getIntervalTime();

    /**
     * Sets the interval.
     *
     * @param integer
     */
    public function setIntervalTime($intervalTime);

    /**
     * Returns the specific dates.
     *
     * @return \DateTime[]
     */
    public function getSpecificDates();

    /**
     * Sets the specific dates.
     *
     * @param array $specificDates
     */
    public function setSpecificDates(array $specificDates);

    /**
     * Returns the periodic interval
     *
     * @return \DateInterval
     */
    public function getDateInterval();

    /**
     * Returns the circle area.
     *
     * @return CircleInterface
     */
    public function getAreaCircle();

    /**
     * Sets the circle area.
     *
     * @param CircleInterface $areaCircle
    */
    public function setAreaCircle(CircleInterface $areaCircle = null);
}
